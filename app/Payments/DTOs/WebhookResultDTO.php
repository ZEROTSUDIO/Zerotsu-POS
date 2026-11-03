<?php

namespace App\Payments\DTOs;

use App\Enums\PaymentStatus;

readonly class WebhookResultDTO
{
    public function __construct(
        public bool $isValidSignature,
        public string $orderNumber,
        public PaymentStatus $internalStatus,
        public ?string $providerTransactionId = null,
        public ?string $providerStatusCode = null,
        public ?string $paymentType = null,
        public ?int $grossAmount = null,
        public array $rawPayload = [],
        public ?string $errorMessage = null
    ) {}

    public static function invalid(string $errorMessage, array $rawPayload = []): self
    {
        return new self(
            isValidSignature: false,
            orderNumber: '',
            internalStatus: PaymentStatus::DENIED,
            rawPayload: $rawPayload,
            errorMessage: $errorMessage
        );
    }
}
