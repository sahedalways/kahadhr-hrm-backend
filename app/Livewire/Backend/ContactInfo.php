<?php

namespace App\Livewire\Backend;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Contact;
use App\Traits\Exportable;
use Livewire\WithFileUploads;

class ContactInfo extends BaseComponent
{
    use WithFileUploads;
    use Exportable;

    public $loaded;
    public $lastId = null;
    public $hasMore = true;
    public $perPage = 10;
    public $sortOrder = 'desc';
    public $search = '';


    public $editMode = false;
    public $contactId;
    public $first_name;
    public $last_name;
    public $phone;
    public $email;
    public $topic;
    public $description;

    protected $listeners = ['deleteItem', 'sortUpdated' => 'handleSort'];

    public function mount()
    {
        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.contact-info', [
            'contacts' => $this->loaded,
        ]);
    }

    /* Load more contacts */
    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Contact::query();

        // Search
        if ($this->search && $this->search != '') {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('phone', 'like', $searchTerm);
            });
        }


        // Load next batch
        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->orderBy('id', $this->sortOrder)
            ->limit($this->perPage)
            ->get();

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }

        if ($items->count()) {
            $this->lastId = $this->sortOrder === 'desc' ? $items->last()->id : $items->first()->id;
            $this->loaded = $this->loaded->merge($items);
        }
    }

    /* Reset loaded contacts */
    private function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    /* Search handler */
    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    /* Sort handler */
    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }


    /* Delete contact */
    public function deleteItem($id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            $this->toast('Contact not found!', 'error');
            return;
        }

        $contact->delete();
        $this->toast('Contact deleted successfully!', 'success');
        $this->resetLoaded();
    }

    /* Export contacts */
    public function exportContacts($type)
    {
        $data = $this->loaded;

        $columns = [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Topic',
            'Description',
        ];

        $keys = [
            'id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'topic',
            'description',
        ];

        return $this->export(
            $data,
            $type,
            'contacts',
            'exports.generic-table-pdf',
            [
                'title' => siteSetting()->site_title . ' - Contact List',
                'columns' => $columns,
                'keys' => $keys
            ]
        );
    }
}
