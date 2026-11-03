<?php

namespace App\Enums;

enum InventoryMovementType: string
{
    case OPENING_BALANCE = 'opening_balance';
    case SALE = 'sale';
    case PURCHASE = 'purchase';
    case REFUND = 'refund';
    case DAMAGE = 'damage';
    case ADJUSTMENT = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::OPENING_BALANCE => 'Saldo Awal',
            self::SALE => 'Penjualan POS',
            self::PURCHASE => 'Pembelian Stok Masuk',
            self::REFUND => 'Pengembalian (Refund)',
            self::DAMAGE => 'Barang Rusak / Kadaluarsa',
            self::ADJUSTMENT => 'Penyesuaian Manual (Opname)',
        };
    }
}
