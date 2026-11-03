<?php

use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('payment_method');
            $table->string('provider')->default('midtrans');
            $table->unsignedBigInteger('amount');
            $table->string('status')->default(PaymentStatus::PENDING->value);
            $table->string('provider_transaction_id')->nullable()->unique(); // Unique for idempotency
            $table->string('provider_reference')->nullable();
            $table->string('payment_type')->nullable(); // qris, gopay, bank_transfer, etc.
            $table->string('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
