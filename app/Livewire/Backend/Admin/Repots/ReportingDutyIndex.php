<?php

namespace App\Livewire\Backend\Admin\Repots;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\ReportingDuty;
use App\Traits\Exportable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ReportingDutyIndex extends BaseComponent
{
    use WithFileUploads, WithPagination;
    use Exportable;

    public $duty_id;
    public $title;
    public $description;
    public $file;
    public $visibility = 'company';

    public $loaded;
    public $perPage = 10;
    public $lastId = null;
    public $existing_file = null;
    public $hasMore = true;
    public $sortOrder = 'desc';
    public $search = '';

    protected $listeners = ['deleteDuty'];

    public function mount()
    {
        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.admin.repots.reporting-duty-index', [
            'duties' => $this->loaded,
        ]);
    }



    public function resetInputFields()
    {
        $this->dispatch('load-description-add');
        $this->duty_id = null;
        $this->title = '';
        $this->description = '';
        $this->file = null;
        $this->visibility = 'company';


        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = ReportingDuty::query();

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $rows = $query->orderBy('id', $this->sortOrder)
            ->limit($this->perPage)
            ->get();

        if ($rows->count() === 0) {
            $this->hasMore = false;
            return;
        }

        $this->lastId = $this->sortOrder === 'desc'
            ? $rows->last()->id
            : $rows->first()->id;

        $this->loaded = $this->loaded->merge($rows);

        if ($rows->count() < $this->perPage) $this->hasMore = false;
    }

    private function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }

    public function saveDuty()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'visibility' => 'required|in:both,company,employee',
        ], [
            'title.required' => 'Please enter the reporting duty title.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'file.required' => 'Please upload a file (PDF, Image, or Document).',
            'file.mimes' => 'The file must be a PDF, Image (jpg, jpeg, png), or Document (doc, docx).',
            'file.max' => 'The file size must not exceed 10MB.',
            'visibility.required' => 'Please select visibility level.',
        ]);

        $filePath = null;
        if ($this->file) {
            $extension = $this->file->getClientOriginalExtension();
            $randomName = rand(10000000, 99999999) . now()->format('is') . '.' . $extension;
            $filePath = $this->file->storeAs('admin/reports/reporting-duties', $randomName, 'public');
        }

        $duty = ReportingDuty::create([
            'title' => $this->title,
            'description' => $this->description,
            'file_path' => $filePath,
            'visibility' => $this->visibility,
        ]);



        $this->toast('Reporting Duty created successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }


    public function deleteDuty($id)
    {
        $duty = ReportingDuty::findOrFail($id);

        // Delete the associated file
        if ($duty->file_path && Storage::disk('public')->exists($duty->file_path)) {
            Storage::disk('public')->delete($duty->file_path);
        }

        $duty->delete();

        $this->toast('Reporting Duty deleted successfully!', 'success');
        $this->resetLoaded();
    }



    public function exportDuties($type)
    {
        $data = $this->loaded->map(function ($duty) {
            return [
                'id' => $duty->id,
                'title' => $duty->title,
                'description' => $duty->description,
                'file_path' => $duty->file_path ? asset('storage/' . $duty->file_path) : 'N/A',
                'visibility' => ucfirst(str_replace('_', ' ', $duty->visibility)),
                'created_at' => Carbon::parse($duty->created_at)->format('d F, Y'),
                'updated_at' => Carbon::parse($duty->updated_at)->format('d F, Y'),
            ];
        });

        return $this->export(
            $data,
            $type,
            'reporting_duties',
            'exports.generic-table-pdf',
            [
                'title' => siteSetting()->site_title . ' - Reporting Duties List',
                'columns' => [
                    'ID',
                    'Title',
                    'Description',
                    'File',
                    'Visibility',
                    'Created At',
                    'Updated At'
                ],
                'keys' => [
                    'id',
                    'title',
                    'description',
                    'file_path',
                    'visibility',
                    'created_at',
                    'updated_at'
                ]
            ]
        );
    }
}
