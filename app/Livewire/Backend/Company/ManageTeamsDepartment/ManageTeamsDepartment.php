<?php

namespace App\Livewire\Backend\Company\ManageTeamsDepartment;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Department;
use App\Models\Team;
use App\Traits\Exportable;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class ManageTeamsDepartment extends BaseComponent
{
    use Exportable, WithPagination;

    public $company_id;
    public $departments, $department_id, $name, $team_id, $editMode = false;
    public $perPage = 10;
    public $sortOrder = 'desc';
    public $statusFilter = '';
    public $loaded;
    public $lastId = null;
    public $hasMore = true;
    public $search;
    public $mode = 'department'; // can be 'department' or 'team'

    protected $listeners = ['deleteDepartment', 'deleteTeam', 'sortUpdated' => 'handleSort'];

    public function mount()
    {
        $this->company_id = auth()->user()->company->id;
        $this->departments = Department::where('company_id', $this->company_id)->get();
        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.company.manage-teams-department.manage-teams-department', [
            'infos' => $this->loaded
        ]);
    }

    /* Reset input fields */
    public function resetInputFields()
    {
        $this->department_id = null;
        $this->name = '';
        $this->team_id = null;
        $this->editMode = false;
        $this->resetErrorBag();
    }



    public function edit($id)
    {
        $this->editMode = true;

        if ($this->mode === 'department') {
            $department = Department::where('company_id', $this->company_id)->find($id);
            if (!$department) {
                $this->toast('Department not found!', 'error');
                return;
            }
            $this->department_id = $department->id;
            $this->name = $department->name;
        } else {
            $team = Team::where('company_id', $this->company_id)->find($id);
            if (!$team) {
                $this->toast('Team not found!', 'error');
                return;
            }
            $this->team_id = $team->id;
            $this->department_id = $team->department_id;
            $this->name = $team->name;
        }
    }




    public function updateDepartment()
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

        $department = Department::where('company_id', $this->company_id)
            ->find($this->department_id);

        if (!$department) {
            $this->toast('Department not found!', 'error');
            return;
        }

        $department->update(['name' => $this->name]);
        $this->toast('Department updated successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }

    public function updateTeam()
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





    /* Save or update item */
    public function save()
    {
        if ($this->mode === 'department') {
            $this->validate([
                'name' => ['required', 'string', 'max:255', Rule::unique('departments')->where('company_id', $this->company_id)->ignore($this->department_id)],
            ]);

            if ($this->editMode) {
                $dept = Department::find($this->department_id);
                $dept->update(['name' => $this->name]);
                $this->toast('Department updated successfully!', 'success');
            } else {
                Department::create(['name' => $this->name, 'company_id' => $this->company_id]);
                $this->toast('Department created successfully!', 'success');
            }
        } else {
            $this->validate([
                'name' => ['required', 'string', 'max:255', Rule::unique('teams')->where('company_id', $this->company_id)->ignore($this->team_id)],
                'department_id' => 'required|exists:departments,id',
            ]);

            if ($this->editMode) {
                $team = Team::find($this->team_id);
                $team->update(['name' => $this->name, 'department_id' => $this->department_id]);
                $this->toast('Team updated successfully!', 'success');
            } else {
                Team::create(['name' => $this->name, 'department_id' => $this->department_id, 'company_id' => $this->company_id]);
                $this->toast('Team created successfully!', 'success');
            }
        }

        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }

    /* Delete item */
    public function deleteDepartment($id)
    {
        Department::where('company_id', $this->company_id)->find($id)?->delete();
        $this->toast('Department deleted successfully!', 'success');
        $this->resetLoaded();
    }

    public function deleteTeam($id)
    {
        Team::where('company_id', $this->company_id)->find($id)?->delete();
        $this->toast('Team deleted successfully!', 'success');
        $this->resetLoaded();
    }

    /* Load more items */
    public function loadMore()
    {
        if (!$this->hasMore) return;

        if ($this->mode === 'department') {
            $query = Department::where('company_id', $this->company_id);
        } else {
            $query = Team::where('company_id', $this->company_id);
        }

        if ($this->search) $query->where('name', 'like', '%' . $this->search . '%');

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

    private function resetLoaded()
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

    /* Switch mode (department/team) */
    public function switchMode($mode)
    {
        $this->mode = $mode;
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
