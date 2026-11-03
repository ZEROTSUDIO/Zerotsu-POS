<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Payments\Gateways\MidtransPaymentGateway;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function __construct(
        protected MidtransPaymentGateway $gateway,
        protected OrderService $orderService
    ) {}

    /**
     * Endpoint penerima Webhook Notification resmi dari Midtrans.
     * URL: POST /api/webhooks/midtrans
     */
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Midtrans Webhook Received', [
            'order_id' => $payload['order_id'] ?? null,
            'status' => $payload['transaction_status'] ?? null,
            'status_code' => $payload['status_code'] ?? null,
        ]);

        // 1. Verifikasi Signature SHA-512 & petakan status
        $result = $this->gateway->handleWebhook($payload);

        if (!$result->isValidSignature) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature key',
            ], 403);
        }

        // 2. Eksekusi perubahan status dan logika bisnis
        if ($result->internalStatus === PaymentStatus::SETTLEMENT) {
            $this->orderService->settlePaymentFromWebhook(
                orderNumber: $result->orderNumber,
                providerTransactionId: $result->providerTransactionId ?? 'UNKNOWN',
                paymentType: $result->paymentType,
                rawPayload: $payload
            );
        } else {
            $this->orderService->updatePaymentStatusFromWebhook(
                orderNumber: $result->orderNumber,
                status: $result->internalStatus,
                providerTransactionId: $result->providerTransactionId,
                rawPayload: $payload
            );
        }

        // 3. Selalu beri HTTP 200 OK ke Midtrans agar tidak melakukan retry berulang
        return response()->json([
            'status' => 'success',
            'message' => 'Notification processed successfully',
            'order_id' => $result->orderNumber,
            'internal_status' => $result->internalStatus->value,
        ], 200);
    }
}
