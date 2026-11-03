<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SETTLEMENT = 'settlement';
    case DENIED = 'denied';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::SETTLEMENT => 'Berhasil (Settlement)',
            self::DENIED => 'Ditolak',
            self::EXPIRED => 'Kedaluwarsa',
            self::CANCELLED => 'Dibatalkan',
            self::REFUNDED => 'Refund',
        };
    }

    public function isSuccess(): bool
    {
        return $this === self::SETTLEMENT;
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::SETTLEMENT, self::DENIED, self::EXPIRED, self::CANCELLED, self::REFUNDED], true);
    }
}
