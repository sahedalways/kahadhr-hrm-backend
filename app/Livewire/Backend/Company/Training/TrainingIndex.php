<?php

namespace App\Livewire\Backend\Company\Training;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Employee;
use App\Models\Training;
use App\Models\User;
use App\Traits\Exportable;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class TrainingIndex extends BaseComponent
{
    use Exportable;
    use WithPagination;

    public $loaded, $lastId = null, $hasMore = true;
    public $perPage = 10;
    public $sortOrder = 'desc';
    public $search = '';

    public $training, $training_id;
    public $course_name, $description, $content_type = 'text';
    public $from_date, $to_date, $expiry_date;
    public $required_proof = false;
    public $company_id;

    public $selectedEmployee;
    public $selectedEmployees = [];

    public $instruction_text;
    public $instruction_file;

    public $require_proof = false;
    public $send_email = false;
    public $employees;

    protected $listeners = ['deleteTraining', 'sortUpdated' => 'handleSort'];

    public function mount()
    {
        $this->company_id = app('authUser')->company->id;
        $this->loaded = collect();


        $this->employees = Employee::where('company_id', $this->company_id)
            ->orderBy('f_name')
            ->get();


        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.company.training.training-index', [
            'infos' => $this->loaded
        ]);
    }

    /* Reset form fields */
    public function resetInputFields()
    {
        $this->training = null;
        $this->training_id = null;
        $this->course_name = '';
        $this->description = '';
        $this->content_type = 'text';
        $this->from_date = null;
        $this->to_date = null;
        $this->expiry_date = null;
        $this->required_proof = false;

        $this->resetErrorBag();
    }

    /* Load more trainings */
    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Training::where('company_id', $this->company_id);

        if ($this->search) {
            $query->where('course_name', 'like', '%' . $this->search . '%');
        }

        if ($this->lastId) {
            $query->where('id', $this->sortOrder == 'desc' ? '<' : '>', $this->lastId);
        }

        $rows = $query->orderBy('id', $this->sortOrder)
            ->limit($this->perPage)
            ->get();

        if ($rows->count() == 0) {
            $this->hasMore = false;
            return;
        }

        if ($rows->count() < $this->perPage) {
            $this->hasMore = false;
        }

        $this->lastId = $this->sortOrder === 'desc'
            ? $rows->last()->id
            : $rows->first()->id;

        $this->loaded = $this->loaded->merge($rows);
    }

    /* Reset loaded */
    private function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    /* Search */
    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    /* Sorting */
    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }

    /* Edit training */
    public function edit($id)
    {
        $this->training = Training::where('company_id', $this->company_id)->find($id);

        if (!$this->training) {
            $this->toast('Training not found!', 'error');
            return;
        }

        $this->training_id     = $this->training->id;
        $this->course_name     = $this->training->course_name;
        $this->description     = $this->training->description;
        $this->content_type    = $this->training->content_type;
        $this->from_date       = $this->training->from_date;
        $this->to_date         = $this->training->to_date;
        $this->expiry_date     = $this->training->expiry_date;
        $this->required_proof  = $this->training->required_proof;
    }

    /* Save Training */
    public function save()
    {
        $this->validate([
            'course_name' => [
                'required',
                Rule::unique('trainings', 'course_name')
                    ->where('company_id', $this->company_id)
                    ->ignore($this->training_id)
            ],
            'content_type' => 'required',
        ]);

        Training::create([
            'company_id'    => $this->company_id,
            'course_name'   => $this->course_name,
            'description'   => $this->description,
            'content_type'  => $this->content_type,
            'file_path'     => $this->file_path,
            'from_date'     => $this->from_date,
            'to_date'       => $this->to_date,
            'expiry_date'   => $this->expiry_date,
            'required_proof' => $this->required_proof,
        ]);

        $this->toast('Training created successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }

    /* Update Training */
    public function update()
    {
        $this->validate([
            'course_name' => [
                'required',
                Rule::unique('trainings', 'course_name')
                    ->where('company_id', $this->company_id)
                    ->ignore($this->training_id)
            ],
        ]);

        $training = Training::find($this->training_id);

        if (!$training) {
            $this->toast('Training not found!', 'error');
            return;
        }

        $training->update([
            'course_name'   => $this->course_name,
            'description'   => $this->description,
            'content_type'  => $this->content_type,
            'file_path'     => $this->file_path,
            'from_date'     => $this->from_date,
            'to_date'       => $this->to_date,
            'expiry_date'   => $this->expiry_date,
            'required_proof' => $this->required_proof,
        ]);

        $this->toast('Training updated successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }

    /* Delete */
    public function deleteTraining($id)
    {
        if ($row = Training::find($id)) {
            $row->delete();
        }

        $this->toast('Training deleted successfully!', 'success');
        $this->resetLoaded();
    }


    public function addEmployee()
    {
        if (!$this->selectedEmployee) return;

        $emp = User::find($this->selectedEmployee);

        if (!$emp) return;

        if (!collect($this->selectedEmployees)->pluck('id')->contains($emp->id)) {
            $this->selectedEmployees[] = [
                'id' => $emp->id,
                'name' => $emp->full_name
            ];
        }

        $this->selectedEmployee = null;
    }

    public function removeEmployee($id)
    {
        $this->selectedEmployees = array_filter($this->selectedEmployees, function ($emp) use ($id) {
            return $emp['id'] !== $id;
        });
    }


    /* Export Training List */
    public function exportTrainings($type)
    {
        $data = $this->loaded->map(function ($t) {
            return [
                'id'            => $t->id,
                'course_name'   => $t->course_name,
                'content_type'  => $t->content_type,
                'from_date'     => $t->from_date,
                'expiry_date'   => $t->expiry_date,
                'created_at'    => Carbon::parse($t->created_at)->format('d F, Y'),
            ];
        });

        return $this->export(
            $data,
            $type,
            'trainings',
            'exports.generic-table-pdf',
            [
                'title' => siteSetting()->site_title . ' - Training List',
                'columns' => [
                    'ID',
                    'Course Name',
                    'Type',
                    'Start Date',
                    'Expiry Date',
                    'Created At'
                ],
                'keys' => [
                    'id',
                    'course_name',
                    'content_type',
                    'from_date',
                    'expiry_date',
                    'created_at'
                ]
            ]
        );
    }
}
