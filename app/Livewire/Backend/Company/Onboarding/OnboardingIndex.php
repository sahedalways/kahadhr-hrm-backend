<?php

namespace App\Livewire\Backend\Company\Onboarding;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Announcement;
use App\Traits\Exportable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class OnboardingIndex extends BaseComponent
{
    use Exportable, WithPagination, WithFileUploads;

    public $announcements, $announcement;
    public $announcement_id, $title, $description, $media;
    public $company_id;

    public $perPage = 10;
    public $sortOrder = 'desc';
    public $statusFilter = '';
    public $search = '';

    public $loaded;
    public $hasMore = true;
    public $lastId = null;

    public $editMode = false;

    public $editId, $oldMedia;

    protected $listeners = ['deleteAnnouncement', 'sortUpdated' => 'handleSort'];

    public function mount()
    {
        $this->company_id = auth()->user()->company->id;

        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.company.onboarding.onboarding-index', [
            'infos' => $this->loaded
        ]);
    }

    public function resetInputFields()
    {
        $this->announcement_id = null;
        $this->title = '';
        $this->description = '';
        $this->media = null;
        $this->oldMedia = null;


        $this->editMode = false;
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                'unique:announcements',
            ],
            'description' => 'required|string',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,mp3,wav|max:50000',
        ]);

        $mediaPath = $this->media ? $this->media->store('announcements', 'public') : null;

        Announcement::create([
            'company_id' => $this->company_id,
            'title' => $this->title,
            'description' => $this->description,
            'media' => $mediaPath,
            'created_by' => auth()->id(),
        ]);

        $this->toast('Announcement created successfully!', 'success');
        $this->dispatch('closemodal');

        $this->resetInputFields();
        $this->resetLoaded();
    }

    public function edit($id)
    {
        $this->editId = $id;
        $this->editMode = true;
        $this->announcement = Announcement::where('company_id', $this->company_id)->find($id);

        if (!$this->announcement) {
            return $this->toast('Announcement not found!', 'error');
        }

        $this->announcement_id = $this->announcement->id;
        $this->title = $this->announcement->title;
        $this->description = $this->announcement->description;
        $this->oldMedia = $this->announcement->media;

        $this->dispatch('load-description-edit', [
            'description' => $this->description,
        ]);
    }

    public function update()
    {
        if (!$this->announcement_id) {
            return $this->toast('No announcement selected!', 'error');
        }

        $this->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('announcements', 'title')
                    ->where('company_id', $this->company_id)
                    ->ignore($this->announcement_id),
            ],
            'description' => 'required|string',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mp3,wav|max:20480',

        ]);

        $announcement = Announcement::find($this->announcement_id);
        if ($this->media) {

            if ($announcement->media && Storage::disk('public')->exists($announcement->media)) {
                Storage::disk('public')->delete($announcement->media);
            }


            if ($this->media instanceof TemporaryUploadedFile) {
                $mediaPath = $this->media->store('announcements', 'public');
            } else {
                $mediaPath = $announcement->media;
            }
        } else {
            $mediaPath = $announcement->media;
        }



        $announcement->update([
            'title' => $this->title,
            'description' => $this->description,
            'media' => $mediaPath,

        ]);

        $this->toast('Announcement updated successfully!', 'success');
        $this->dispatch('closemodal');

        $this->resetInputFields();
        $this->resetLoaded();
    }

    public function deleteAnnouncement($id)
    {
        $announcement = Announcement::where('company_id', $this->company_id)->find($id);

        if ($announcement) {

            if ($announcement->media) {

                Storage::delete($announcement->media);
            }

            // Delete announcement
            $announcement->delete();
        }

        $this->toast('Announcement deleted successfully!', 'success');
        $this->resetLoaded();
    }

    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Announcement::where('company_id', $this->company_id);

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }

        if ($this->statusFilter !== '') {
            $query->where('onboarding', $this->statusFilter);
        }

        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->orderBy('id', $this->sortOrder)
            ->limit($this->perPage)
            ->get();

        if ($items->count() == 0) {
            $this->hasMore = false;
            return;
        }

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }

        $this->lastId = $this->sortOrder === 'desc'
            ? $items->last()->id
            : $items->first()->id;

        $this->loaded = $this->loaded->merge($items);
    }




    public function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }

    public function handleFilter($value)
    {
        $this->statusFilter = $value;
        $this->resetLoaded();
    }
}
