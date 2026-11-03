<?php

namespace App\Livewire;

use App\Models\Contact;
use Livewire\Component;
use Livewire\Features\SupportPagination\WithoutUrlPagination;
use Livewire\WithPagination;

class Contacts extends Component
{
    use WithoutUrlPagination;
    use WithPagination;

    public $search = '';
    public ?Contact $viewingContact = null;
    public $replyMessage = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search']);
        $this->resetPage();
    }

    public function seeDetails($id)
    {
        $this->viewingContact = Contact::with('replies.admin')->findOrFail($id);
        $this->replyMessage = '';
    }

    public function closeDetails()
    {
        $this->viewingContact = null;
        $this->replyMessage = '';
    }

    public function sendReply()
    {
        $this->validate([
            'replyMessage' => 'required|string|min:5',
        ]);

        $reply = $this->viewingContact->replies()->create([
            'admin_id' => auth()->id(),
            'message' => $this->replyMessage,
            'sent_via_email' => true,
        ]);

        // Update status if it's new
        if ($this->viewingContact->status === 'new') {
            $this->viewingContact->update(['status' => 'replied']);
        }

        try {
            \Illuminate\Support\Facades\Mail::to($this->viewingContact->email)
                ->send(new \App\Mail\ContactReplyMail($this->replyMessage, $this->viewingContact->subject));
        } catch (\Exception $e) {
            session()->flash('error', 'Reply saved but email failed to send: ' . $e->getMessage());
        }

        $this->replyMessage = '';
        $this->viewingContact->refresh();
        session()->flash('status', 'Reply sent successfully.');
    }

    public function deleteContact($id)
    {
        $contact = Contact::findOrFail($id);

        $contact->delete();

        if ($this->viewingContact && $this->viewingContact->id == $id) {
            $this->closeDetails();
        }

        session()->flash('status', 'Contact successfully deleted.');
    }

    public function render()
    {
        $query = Contact::query();
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }
        return view('livewire.contacts', [
            'contacts' => $query->latest()->paginate(5),
        ]);
    }
}
