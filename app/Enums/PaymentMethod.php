<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case MIDTRANS_SNAP = 'midtrans_snap';
    case MIDTRANS_QRIS = 'midtrans_qris';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Tunai (Cash)',
            self::MIDTRANS_SNAP => 'Midtrans Snap (Online)',
            self::MIDTRANS_QRIS => 'QRIS Dinamis',
        };
    }
}
