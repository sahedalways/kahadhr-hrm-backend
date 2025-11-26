<?php

namespace App\Livewire\Backend\Company\ManageDepartments;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Department;
use App\Traits\Exportable;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class ManageDepartments extends BaseComponent
{
    use Exportable;
    use WithPagination;

    public $departments, $department, $department_id, $name, $company_id;
    public $perPage = 10;
    public $sortOrder = 'desc';
    public $statusFilter = '';
    public $loaded;
    public $lastId = null;
    public $hasMore = true;
    public $editMode = false;
    public $search;

    protected $listeners = ['deleteDepartment', 'sortUpdated' => 'handleSort'];

    public function mount()
    {
        $this->company_id = app('authUser')->company->id;
        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.company.manage-departments.manage-departments', [
            'infos' => $this->loaded
        ]);
    }

    /* Reset input fields */
    public function resetInputFields()
    {
        $this->department = null;
        $this->department_id = null;
        $this->name = '';
        $this->editMode = false;
        $this->resetErrorBag();
    }


    public function edit($id)
    {
        $this->editMode = true;
        $this->department = Department::where('company_id', $this->company_id)->find($id);

        if (!$this->department) {
            $this->toast('Department not found!', 'error');
            return;
        }

        $this->department_id = $this->department->id;
        $this->name = $this->department->name;
    }


    public function update()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')
                    ->where('company_id', $this->company_id)
                    ->ignore($this->department_id),
            ],
        ]);

        if (!$this->department_id) {
            $this->toast('No department selected!', 'error');
            return;
        }

        $department = Department::where('company_id', $this->company_id)
            ->find($this->department_id);

        if (!$department) {
            $this->toast('Department not found!', 'error');
            return;
        }

        $department->update([
            'name' => $this->name,
        ]);

        $this->toast('Department updated successfully!', 'success');
        $this->dispatch('closemodal');

        // Reset input after update
        $this->resetInputFields();

        $this->resetLoaded();
    }

    /* Create or update department */
    public function save()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')
                    ->where('company_id', $this->company_id)
                    ->ignore($this->department_id),
            ],
        ]);


        $data = [
            'name' => $this->name,
            'company_id' => auth()->user()->company->id,
        ];

        Department::create($data);

        $this->toast('Department created successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }

    /* Delete department */
    public function deleteDepartment($id)
    {
        $department = Department::where('company_id', $this->company_id)->find($id);
        if ($department) $department->delete();

        $this->toast('Department deleted successfully!', 'success');
        $this->resetLoaded();
    }


    /* Search & reset */
    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    /* Load more departments */
    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Department::where('company_id', $this->company_id);

        if ($this->search && $this->search != '') {
            $searchTerm = '%' . $this->search . '%';
            $query->where('name', 'like', $searchTerm);
        }

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->orderBy('id', $this->sortOrder)->limit($this->perPage)->get();

        if ($items->count() == 0) {
            $this->hasMore = false;
            return;
        }

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }

        $this->lastId = $this->sortOrder === 'desc' ? $items->last()->id : $items->first()->id;
        $this->loaded = $this->loaded->merge($items);
    }

    /* Reset loaded departments */
    private function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    /* Sorting & filtering */
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



    public function exportDepartments($type)
    {
        // Map the loaded departments
        $data = $this->loaded->map(function ($dept) {
            return [
                'id' => $dept->id,
                'name' => $dept->name,

                'created_at' => $dept->created_at ? Carbon::parse($dept->created_at)->format('d F, Y') : 'N/A',
                'updated_at' => $dept->updated_at ? Carbon::parse($dept->updated_at)->format('d F, Y') : 'N/A',
            ];
        });



        $columns = [
            'Department ID',
            'Department Name',
            'Created At',
            'Updated At',
        ];

        $keys = [
            'id',
            'name',

            'created_at',
            'updated_at',
        ];

        return $this->export(
            $data,
            $type,
            'departments',
            'exports.generic-table-pdf',
            [
                'title' => siteSetting()->site_title . ' - Department List',
                'columns' => $columns,
                'keys' => $keys,
            ]
        );
    }
}
