<?php

namespace App\Enums;

enum OrderStatus: string
{
    case DRAFT = 'draft';
    case PENDING_PAYMENT = 'pending_payment';
    case PAID = 'paid';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';
    case PARTIALLY_REFUNDED = 'partially_refunded';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PENDING_PAYMENT => 'Menunggu Pembayaran',
            self::PAID => 'Lunas',
            self::CANCELLED => 'Dibatalkan',
            self::REFUNDED => 'Dikembalikan (Refund)',
            self::PARTIALLY_REFUNDED => 'Refund Sebagian',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PAID => 'bg-emerald-100 text-emerald-900 border-emerald-900',
            self::PENDING_PAYMENT => 'bg-amber-100 text-amber-900 border-amber-900',
            self::CANCELLED, self::REFUNDED => 'bg-rose-100 text-rose-900 border-rose-900',
            default => 'bg-slate-100 text-slate-900 border-slate-900',
        };
    }
}
