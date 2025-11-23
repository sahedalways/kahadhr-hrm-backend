<?php

namespace App\Livewire\Backend;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Contact;

class ContactInfo extends BaseComponent
{
    public $search;
    public $perPage = 10;
    public $loaded;
    public $lastId = null;
    public $hasMore = true;

    public $editMode = false;
    public $contactId;
    public $first_name;
    public $last_name;
    public $phone;
    public $email;
    public $topic;
    public $description;
    protected $listeners = ['deleteItem'];

    public function mount()
    {
        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.contact-info', [
            'contacts' => $this->loaded
        ]);
    }

    // Search contacts
    public function searchContact()
    {
        $this->resetLoaded();
    }

    // Load more function
    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Contact::query(); // Contact model

        if ($this->search && $this->search != '') {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('topic', 'like', '%' . $search . '%');
            });
        }

        if ($this->lastId) {
            $query->where('id', '<', $this->lastId);
        }

        $items = $query->orderBy('id', 'desc')
            ->limit($this->perPage)
            ->get();

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }

        if ($items->count()) {
            $this->lastId = $items->last()->id;
            $this->loaded = $this->loaded->merge($items);
        }
    }

    // Reset loaded collection
    private function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    // Delete contact
    public function deleteItem($id)
    {
        $item = Contact::find($id);

        if ($item) {
            $item->delete();
            $this->toast('Contact item has been deleted!', 'success');
            $this->resetLoaded();
        } else {
            $this->toast('Contact item not found!', 'error');
        }
    }
}
