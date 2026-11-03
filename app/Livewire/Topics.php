<?php

namespace App\Livewire;

use App\Models\Topic;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class Topics extends Component
{
    use WithFileUploads;
    use WithoutUrlPagination;
    use WithPagination;

    public $isCreating = false;

    public $topicId;

    public $name;

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

    public function addtopic()
    {
        $this->reset(['topicId', 'name']);
        $this->isCreating = true;
    }

    public function cancel()
    {
        $this->isCreating = false;
        $this->reset(['topicId', 'name']);
    }

    public function createtopic()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
        ]);

        Topic::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        $this->isCreating = false;
        $this->reset(['name']);
        session()->flash('status', 'topic successfully created.');
    }

    public function updatetopic()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $topic = Topic::findOrFail($this->topicId);

        $data = [
            'name' => $this->name,
            'slug' => Str::slug($this->name),
        ];

        $topic->update($data);

        $this->isCreating = false;
        $this->reset(['topicId', 'name']);
        session()->flash('status', 'topic successfully updated.');
    }

    public function edittopic($id)
    {
        $topic = Topic::findOrFail($id);
        $this->topicId = $id;
        $this->isCreating = true;
        $this->resetValidation();
        $this->resetErrorBag();

        $this->name = $topic->name;
    }

    public function deletetopic($id)
    {
        $topic = Topic::findOrFail($id);

        $topic->delete();
        session()->flash('status', 'topic successfully deleted.');
    }

    public function render()
    {
        $query = Topic::query();

        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        return view('livewire.topics', [
            'topics' => $query->latest()->paginate(10),
        ]);
    }
}
