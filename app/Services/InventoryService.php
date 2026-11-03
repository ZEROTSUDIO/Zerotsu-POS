<?php

namespace App\Services;

use App\Enums\InventoryMovementType;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Memotong stok produk untuk setiap item di order yang lunas.
     * Menggunakan Pessimistic Locking (lockForUpdate) untuk mencegah overselling.
     */
    public function deductStockForOrder(Order $order, ?User $actor = null): void
    {
        // Pastikan tidak dobel potong jika sudah pernah dicatat untuk order ini
        $alreadyDeducted = InventoryMovement::where('reference_type', Order::class)
            ->where('reference_id', $order->id)
            ->where('type', InventoryMovementType::SALE)
            ->exists();

        if ($alreadyDeducted) {
            Log::info("Stok untuk order {$order->order_number} sudah pernah dipotong. Melewati pemotongan ulang.");
            return;
        }

        DB::transaction(function () use ($order, $actor) {
            foreach ($order->items as $item) {
                if (!$item->product_id) {
                    continue;
                }

                // Pessimistic Lock pada baris produk
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();

                if (!$product) {
                    continue;
                }

                $stockBefore = $product->stock;
                $stockAfter = $stockBefore - $item->quantity;

                // Update saldo cached
                $product->update([
                    'stock' => $stockAfter,
                ]);

                // Catat ke ledger audit inventory_movements
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => InventoryMovementType::SALE,
                    'quantity' => -$item->quantity, // minus karena keluar
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'note' => "Penjualan POS Order #{$order->order_number}",
                    'created_by' => $actor?->id ?? $order->cashier_id,
                ]);
            }
        });
    }

    /**
     * Mengembalikan stok jika order dibatalkan atau di-refund.
     */
    public function restoreStockForOrder(Order $order, string $reason = 'Refund order', ?User $actor = null): void
    {
        DB::transaction(function () use ($order, $reason, $actor) {
            foreach ($order->items as $item) {
                if (!$item->product_id) {
                    continue;
                }

                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();

                if (!$product) {
                    continue;
                }

                $stockBefore = $product->stock;
                $stockAfter = $stockBefore + $item->quantity;

                $product->update([
                    'stock' => $stockAfter,
                ]);

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => InventoryMovementType::REFUND,
                    'quantity' => $item->quantity, // plus karena kembali
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference_type' => Order::class,
                    'reference_id' => $order->id,
                    'note' => "Pengembalian stok: {$reason} (Order #{$order->order_number})",
                    'created_by' => $actor?->id,
                ]);
            }
        });
    }
}
