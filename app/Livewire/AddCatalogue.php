<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class AddCatalogue extends Component
{
    use WithFileUploads;
    #[Validate('required|min:3')]
    public $name;
    #[Validate('required|image|max:10240')]
    public $image;
    #[Validate('required|min:10')]
    public $description;
    #[Validate('required|exists:categories,id')]
    public $category_id;

    public function createCatalogue()
    {
        $validated = $this->validate();
        if ($this->image) {
            $validated['image'] = $this->image->store('products', 'public');
        }
        Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'category_id' => $validated['category_id'],
            'image' => $validated['image'],
            'description' => $validated['description'],
        ]);
        $this->reset();
        session()->flash('status', 'Product successfully created.');
    }

    public function render()
    {
        return view('livewire.add-catalogue', [
            'categories' => Category::all()
        ]);
    }
}
