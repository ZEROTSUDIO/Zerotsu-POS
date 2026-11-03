<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.pos')]
#[Title('ZerotsuPOS // Terminal 01')]
class PosTerminal extends Component
{
    public string $terminalId = 'TERMINAL-01';
    public string $shiftNumber = 'SHIFT-01';

    public function render()
    {
        return view('livewire.pos-terminal', [
            'categoriesCount' => Category::count(),
            'productsCount' => Product::count(),
        ]);
    }
}
