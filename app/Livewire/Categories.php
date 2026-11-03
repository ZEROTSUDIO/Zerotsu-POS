<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithFileUploads;
    use WithoutUrlPagination;
    use WithPagination;

    public $isCreating = false;

    public $categoryId;

    public $name;

    public $description;

    public $image;

    // Filter Properties
    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    public function addCategory()
    {
        $this->reset(['categoryId', 'name', 'description', 'image']);
        $this->isCreating = true;
    }

    public function cancel()
    {
        $this->isCreating = false;
        $this->reset(['categoryId', 'name', 'description', 'image']);
    }

    public function createCategory()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
        ]);

        $imagePath = null;
        if ($this->image) {
            $slug = Str::slug($validated['name']);
            $extension = strtolower($this->image->getClientOriginalExtension());
            $filename = $slug . '-' . substr(md5(uniqid()), 0, 6) . '.' . $extension;

            // Process image
            $processedImage = Image::read($this->image->getRealPath());

            // Encode based on format
            if ($extension === 'png') {
                $encoded = $processedImage->toPng();
            } elseif (in_array($extension, ['jpg', 'jpeg'])) {
                $encoded = $processedImage->toJpeg(85);
            } elseif ($extension === 'webp') {
                $encoded = $processedImage->toWebp(85);
            } else {
                $encoded = $processedImage->encode();
            }

            $imagePath = 'categories/' . $filename;
            Storage::disk('public_direct')->put($imagePath, (string) $encoded);
        }

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'image' => $imagePath,
        ]);

        $this->isCreating = false;
        $this->reset(['name', 'description', 'image']);
        session()->flash('status', 'Category successfully created.');
    }

    public function updateCategory()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
        ]);

        $category = Category::findOrFail($this->categoryId);

        $data = [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
        ];

        if ($this->image) {
            // Delete old image
            if ($category->image && Storage::disk('public_direct')->exists($category->image)) {
                Storage::disk('public_direct')->delete($category->image);
            }

            $slug = Str::slug($this->name);
            $extension = strtolower($this->image->getClientOriginalExtension());
            $filename = $slug . '-' . substr(md5(uniqid()), 0, 6) . '.' . $extension;

            // Process image
            $processedImage = Image::read($this->image->getRealPath());

            // Encode based on format
            if ($extension === 'png') {
                $encoded = $processedImage->toPng();
            } elseif (in_array($extension, ['jpg', 'jpeg'])) {
                $encoded = $processedImage->toJpeg(85);
            } elseif ($extension === 'webp') {
                $encoded = $processedImage->toWebp(85);
            } else {
                $encoded = $processedImage->encode();
            }

            $path = 'categories/' . $filename;
            Storage::disk('public_direct')->put($path, (string) $encoded);

            $data['image'] = $path;
        }

        $category->update($data);

        $this->isCreating = false;
        $this->reset(['categoryId', 'name', 'description', 'image']);
        session()->flash('status', 'Category successfully updated.');
    }

    public function editCategory($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $id;
        $this->isCreating = true;
        $this->resetValidation();
        $this->resetErrorBag();

        $this->name = $category->name;
        $this->description = $category->description;
        // Don't set image property as it expects a temporary uploaded file object
        // We handle displaying the existing image in the view logic
        $this->reset('image');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);

        if ($category->image && Storage::disk('public_direct')->exists($category->image)) {
            Storage::disk('public_direct')->delete($category->image);
        }

        $category->delete();
        session()->flash('status', 'Category successfully deleted.');
    }

    public function render()
    {
        $query = Category::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        return view('livewire.categories', [
            'categories' => $query->latest()->paginate(10),
        ]);
    }
}
