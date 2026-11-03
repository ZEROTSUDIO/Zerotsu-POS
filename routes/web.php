<?php

use App\Livewire\PosTerminal;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('pos');
});

Route::get('/pos', PosTerminal::class)->name('pos');

