<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\Topic;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Articles extends Component
{
    use WithFileUploads;

    public $isCreating = false;

    #[Validate('required|min:3')]
    public $title;

    #[Validate('required|min:10')]
    public $content;

    #[Validate('required|exists:topics,id')]
    public $topic_id;

    #[Validate('required|in:draft,published')]
    public $status = 'draft';

    public $articleId = null;

    public $image;

    public $imagePreview;

    public $existingImage;

    // search
    public $search = '';

    public $topicFilter = '';

    public $statusFilter = '';

    public $perPage = 8;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTopicFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'topicFilter', 'statusFilter']);
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function addArticle()
    {
        $this->reset(['title', 'topic_id', 'content', 'image', 'status']);
        $this->status = 'draft';
        $this->isCreating = true;
    }

    public function cancel()
    {
        $this->isCreating = false;
        $this->reset(['title', 'topic_id', 'content', 'image', 'articleId', 'status']);
        $this->dispatch('article-form-closed');
    }

    public function createArticle()
    {
        $validated = $this->validate([
            'title' => 'required|min:3',
            'content' => 'required|min:10',
            'topic_id' => 'required|exists:topics,id',
            'image' => 'nullable|image|max:5120',
            'status' => 'required|in:draft,published',
        ]);
        $imagePath = null;
        if ($this->image) {
            $slug = Str::slug($validated['title']);
            $extension = strtolower($this->image->getClientOriginalExtension());
            $filename = $slug.'.'.$extension;

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

            $imagePath = 'articles/'.$filename;
            Storage::disk('public_direct')->put($imagePath, (string) $encoded);
        }
        Article::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'body' => $validated['content'],
            'topic_id' => $validated['topic_id'],
            'author_id' => auth()->id(),
            'image' => $imagePath,
            'status' => $validated['status'],
        ]);

        $this->isCreating = false;
        $this->reset(['title', 'topic_id', 'content', 'image', 'status']);
        $this->dispatch('article-form-closed');
        session()->flash('status', 'Article successfully created.');
    }

    public function saveArticle($status = null)
    {
        if ($status) {
            $this->status = $status;
        }

        if ($this->articleId) {
            $this->updateArticle();
        } else {
            $this->createArticle();
        }
    }

    public function updateArticle()
    {
        $validated = $this->validate([
            'title' => 'required|min:3',
            'content' => 'required|min:10',
            'topic_id' => 'required|exists:topics,id',
            'image' => 'nullable|image|max:5120',
            'status' => 'required|in:draft,published',
        ]);

        $article = Article::findOrFail($this->articleId);
        $data = [
            'title' => $this->title,
            'slug' => Str::slug($this->title),
            'body' => $this->content,
            'topic_id' => $this->topic_id,
            'status' => $this->status,
        ];

        if ($this->image) {
            if ($article->image && Storage::disk('public_direct')->exists($article->image)) {
                Storage::disk('public_direct')->delete($article->image);
            }

            $slug = Str::slug($validated['title']);
            $extension = strtolower($this->image->getClientOriginalExtension());
            $filename = $slug.'.'.$extension;

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

            $imagePath = 'articles/'.$filename;
            Storage::disk('public_direct')->put($imagePath, (string) $encoded);
            $data['image'] = $imagePath;
        }
        $article->update($data);

        $this->isCreating = false;
        $this->reset(['title', 'topic_id', 'content', 'image', 'articleId', 'status']);
        $this->dispatch('article-form-closed');
        session()->flash('status', 'Article successfully updated.');
    }

    public function editArticle($id)
    {
        $article = Article::findOrFail($id);
        $this->articleId = $id;
        $this->isCreating = true;
        $this->resetValidation();
        $this->resetErrorBag();
        $this->title = $article->title;
        $this->content = $article->body;
        $this->topic_id = $article->topic_id;
        $this->status = $article->status->value;
        $this->reset(['image', 'imagePreview']);
        $this->existingImage = $article->image;
    }

    public function deleteArticle($id)
    {
        $article = Article::findOrFail($id);
        if ($article->image && Storage::disk('public_direct')->exists($article->image)) {
            Storage::disk('public_direct')->delete($article->image);
        }
        $article->delete();
        session()->flash('status', 'Article successfully deleted.');
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'image|max:5120',
        ]);

        $this->imagePreview = $this->image->temporaryUrl();
    }

    public function removeImage()
    {
        $this->image = null;
        $this->imagePreview = null;
        $this->existingImage = null;
    }

    public function render()
    {
        $query = Article::query();
        if ($this->search) {
            $query->where('title', 'like', '%'.$this->search.'%');
        }
        if ($this->topicFilter) {
            $query->where('topic_id', $this->topicFilter);
        }
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        $articles = $query->latest()->paginate($this->perPage);

        return view('livewire.articles', [
            'articles' => $articles,
            'topics' => Topic::all(),
        ]);
    }
}
