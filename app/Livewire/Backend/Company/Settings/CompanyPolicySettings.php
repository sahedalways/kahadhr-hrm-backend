<?php

namespace App\Livewire\Backend\Company\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CompanyPolicy;
use App\Models\Employee;
use App\Models\EmailSetting;
use App\Traits\Exportable;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CompanyPolicySettings extends BaseComponent
{
    use WithFileUploads, WithPagination;
    use Exportable;

    public $company_id;
    public $policy_id;
    public $title;
    public $description;
    public $file;
    public $send_email = false;
    public $emailGatewayMissing = false;

    public $employees;
    public $loaded;
    public $perPage = 10;
    public $lastId = null;
    public $existing_file = null;
    public $hasMore = true;
    public $sortOrder = 'desc';
    public $search = '';

    protected $listeners = ['deletePolicy'];

    public function mount()
    {
        $this->company_id = app('authUser')->company->id;

        $this->employees = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->orderBy('f_name')
            ->get();

        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.company.settings.company-policy-settings', [
            'policies' => $this->loaded,
        ]);
    }

    public function updatedSendEmail($value)
    {
        if ($value) {
            $gateway = EmailSetting::where('company_id', $this->company_id)->first();
            $this->emailGatewayMissing = $gateway ? false : true;
            if (!$gateway) $this->send_email = false;
        } else {
            $this->emailGatewayMissing = false;
        }
    }

    public function resetInputFields()
    {
        $this->dispatch('load-description-add');
        $this->policy_id = null;
        $this->title = '';
        $this->description = '';
        $this->file = null;
        $this->send_email = false;
        $this->emailGatewayMissing = false;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = CompanyPolicy::where('company_id', $this->company_id);

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

    public function savePolicy()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|mimes:pdf,jpg,jpeg,png|max:10240',
            'send_email' => 'boolean',
        ], [
            'title.required' => 'Please enter the policy title.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'file.required' => 'Please upload a PDF or Image file.',
            'file.mimes' => 'The file must be a PDF or an Image (jpg, jpeg, png).',
            'file.max' => 'The file size must not exceed 10MB.',
        ]);



        $filePath = null;
        if ($this->file) {
            $extension = $this->file->getClientOriginalExtension();
            $randomName = rand(10000000, 99999999) . now()->format('is') . '.' . $extension;
            $filePath = $this->file->storeAs('company/policies', $randomName, 'public');
        }

        $policy = CompanyPolicy::create([
            'company_id' => $this->company_id,
            'title' => $this->title,
            'description' => $this->description,
            'file_path' => $filePath,
        ]);


        // if ($this->send_email) {
        //     foreach ($this->employees as $emp) {

        //     }
        // }

        $this->toast('Company Policy created successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }



    public function edit($id)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $policy = CompanyPolicy::where('company_id', $this->company_id)->findOrFail($id);

        $this->policy_id = $policy->id;
        $this->title = $policy->title;
        $this->description = $policy->description;
        $this->file = $policy->file_path;


        $this->dispatch('load-description-edit', description: $this->description);
    }
    public function updatePolicy()
    {
        $policy = CompanyPolicy::findOrFail($this->policy_id);

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'send_email' => 'boolean',
        ];


        if ($this->file instanceof UploadedFile) {
            $rules['file'] = 'mimes:pdf,jpg,jpeg,png|max:10240';
        }

        $this->validate($rules, [
            'title.required' => 'Please enter the policy title.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'file.mimes' => 'The file must be a PDF or an Image (jpg, jpeg, png).',
            'file.max' => 'The file size must not exceed 10MB.',
        ]);


        if ($this->file instanceof UploadedFile) {

            if ($policy->file_path && Storage::disk('public')->exists($policy->file_path)) {
                Storage::disk('public')->delete($policy->file_path);
            }

            $extension = $this->file->getClientOriginalExtension();
            $randomName = rand(10000000, 99999999) . now()->format('is') . '.' . $extension;

            $filePath = $this->file->storeAs('company/policies', $randomName, 'public');

            $policy->file_path = $filePath;
        }

        $policy->update([
            'title' => $this->title,
            'description' => $this->description,
        ]);

        $this->toast('Company Policy updated successfully!', 'success');
        $this->resetInputFields();
        $this->dispatch('closemodal');
        $this->resetLoaded();
    }

    public function deletePolicy($id)
    {
        $policy = CompanyPolicy::findOrFail($id);

        if ($policy->file_path && Storage::disk('public')->exists($policy->file_path)) {
            Storage::disk('public')->delete($policy->file_path);
        }

        $policy->delete();

        $this->toast('Company Policy deleted successfully!', 'success');
        $this->resetLoaded();
    }




    public function exportPolicies($type)
    {
        $data = $this->loaded->map(function ($policy) {
            return [
                'id'          => $policy->id,
                'company_id'  => $policy->company_id,
                'title'       => $policy->title,
                'description' => $policy->description,
                'file_path'   => $policy->file_path ? asset('storage/' . $policy->file_path) : 'N/A',
                'send_email'  => $policy->send_email ? 'Yes' : 'No',
                'created_at'  => Carbon::parse($policy->created_at)->format('d F, Y'),
            ];
        });

        return $this->export(
            $data,
            $type,
            'company_policies',
            'exports.generic-table-pdf',
            [
                'title' => siteSetting()->site_title . ' - Company Policy List',
                'columns' => [
                    'ID',
                    'Company ID',
                    'Title',
                    'Description',
                    'File',
                    'Sent via Email',
                    'Created At'
                ],
                'keys' => [
                    'id',
                    'company_id',
                    'title',
                    'description',
                    'file_path',
                    'send_email',
                    'created_at'
                ]
            ]
        );
    }
}
