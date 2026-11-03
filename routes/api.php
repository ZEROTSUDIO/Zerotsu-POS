<?php

use App\Http\Controllers\Api\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/midtrans', MidtransWebhookController::class)->name('webhooks.midtrans');
