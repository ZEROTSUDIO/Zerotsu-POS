<?php

namespace App\Payments\DTOs;

use App\Enums\PaymentStatus;

readonly class PaymentResultDTO
{
    public function __construct(
        public bool $success,
        public PaymentStatus $status,
        public ?string $providerTransactionId = null,
        public ?string $providerReference = null,
        public ?string $snapToken = null,
        public ?string $redirectUrl = null,
        public ?string $paymentType = null,
        public ?string $message = null,
        public array $rawPayload = []
    ) {}

    public static function successful(
        PaymentStatus $status,
        ?string $providerTransactionId = null,
        ?string $snapToken = null,
        ?string $redirectUrl = null,
        ?string $paymentType = null,
        array $rawPayload = []
    ): self {
        return new self(
            success: true,
            status: $status,
            providerTransactionId: $providerTransactionId,
            snapToken: $snapToken,
            redirectUrl: $redirectUrl,
            paymentType: $paymentType,
            rawPayload: $rawPayload
        );
    }

    public static function failed(string $message, array $rawPayload = []): self
    {
        return new self(
            success: false,
            status: PaymentStatus::DENIED,
            message: $message,
            rawPayload: $rawPayload
        );
    }
}
