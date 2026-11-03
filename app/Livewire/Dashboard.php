<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard', [
            'totalProducts' => Product::count(),
            'totalArticles' => Article::count(),
            'totalCategories' => Category::count(),
            'totalNewContacts' => Contact::new()->count(),
            'recentContacts' => Contact::latest()->take(5)->get(),
            'recentProducts' => Product::latest()->take(5)->get(),
        ]);
    }
}
