<?php

namespace App\Livewire\Backend\Company\ManageTeams;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Team;
use App\Models\Department;
use App\Traits\Exportable;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class ManageTeams extends BaseComponent
{
    use Exportable, WithPagination;

    public $departments, $department_id, $name, $company_id, $team_id;
    public $perPage = 10;
    public $sortOrder = 'desc';
    public $statusFilter = '';
    public $loaded;
    public $lastId = null;
    public $hasMore = true;
    public $editMode = false;
    public $search;

    protected $listeners = ['deleteTeam', 'sortUpdated' => 'handleSort'];

    public function mount()
    {
        $this->company_id = app('authUser')->company->id;
        $this->departments = Department::where('company_id', $this->company_id)->get();
        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.company.manage-teams.manage-teams', [
            'infos' => $this->loaded,
        ]);
    }

    /* Reset input fields */
    public function resetInputFields()
    {
        $this->department_id = null;
        $this->name = '';
        $this->editMode = false;
        $this->resetErrorBag();
    }

    /* Edit team */
    public function edit($id)
    {
        $this->editMode = true;
        $team = Team::where('company_id', $this->company_id)->find($id);

        if (!$team) {
            $this->toast('Team not found!', 'error');
            return;
        }

        $this->department_id = $team->department_id;
        $this->name = $team->name;
        $this->team_id = $team->id;
    }

    /* Update team */
    public function update()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('teams', 'name')
                    ->where('company_id', $this->company_id)
                    ->ignore($this->team_id),
            ],
            'department_id' => 'required|exists:departments,id',
        ]);

        $team = Team::where('company_id', $this->company_id)->find($this->team_id);
        if (!$team) {
            $this->toast('Team not found!', 'error');
            return;
        }

        $team->update([
            'name' => $this->name,
            'department_id' => $this->department_id,
        ]);

        $this->toast('Team updated successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }

    /* Create new team */
    public function save()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('teams', 'name')
                    ->where('company_id', $this->company_id),
            ],
            'department_id' => 'required|exists:departments,id',
        ]);

        Team::create([
            'name' => $this->name,
            'department_id' => $this->department_id,
            'company_id' => $this->company_id,
        ]);

        $this->toast('Team created successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }

    /* Delete team */
    public function deleteTeam($id)
    {
        $team = Team::where('company_id', $this->company_id)->find($id);
        if ($team) $team->delete();

        $this->toast('Team deleted successfully!', 'success');
        $this->resetLoaded();
    }

    /* Search & reset */
    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    /* Load more teams */
    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Team::where('company_id', $this->company_id);

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

    /* Reset loaded teams */
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

    /* Export teams */
    public function exportTeams($type)
    {
        $data = $this->loaded->map(function ($team) {
            return [
                'id' => $team->id,
                'name' => $team->name,
                'department_name' => $team->department ? $team->department->name : 'N/A',
                'company_name' => $team->company ? $team->company->company_name : 'N/A',
                'created_at' => $team->created_at ? Carbon::parse($team->created_at)->format('d F, Y') : 'N/A',
                'updated_at' => $team->updated_at ? Carbon::parse($team->updated_at)->format('d F, Y') : 'N/A',
            ];
        });

        $columns = [
            'Team ID',
            'Team Name',
            'Department Name',
            'Company Name',
            'Created At',
            'Updated At',
        ];

        $keys = [
            'id',
            'name',
            'department_name',
            'company_name',
            'created_at',
            'updated_at',
        ];

        return $this->export(
            $data,
            $type,
            'teams',
            'exports.generic-table-pdf',
            [
                'title' => siteSetting()->site_title . ' - Team List',
                'columns' => $columns,
                'keys' => $keys,
            ]
        );
    }
}
