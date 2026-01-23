<?php

namespace App\Livewire\Backend\Company\Training;

use App\Events\NotificationEvent;
use App\Jobs\SendTrainingNotification;
use App\Jobs\SendTrainingReminder;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\EmailSetting;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Training;
use App\Models\TrainingAssignment;
use App\Models\User;
use App\Traits\Exportable;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;

class TrainingIndex extends BaseComponent
{
    use Exportable;
    use WithPagination;
    use WithFileUploads;

    public $loaded, $lastId = null, $hasMore = true;
    public $perPage = 10;
    public $sortOrder = 'desc';
    public $search = '';

    public $training, $training_id;
    public $course_name, $description, $content_type = 'video';
    public $from_date, $to_date, $expiry_date;
    public $company_id;

    public $selectedEmployee;
    public $selectedEmployees = [];

    public $instruction_text;
    public $instruction_file;

    public bool $require_proof = false;
    public $send_email = false;
    public $openTrainingId = null;
    public $employees;

    public $trainingId;
    public $emailGatewayMissing = false;

    protected $listeners = ['deleteTraining', 'sortUpdated' => 'handleSort'];



    protected $rules = [
        'selectedEmployees' => 'required|array|min:1',
        'course_name' => 'required|string',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
        'expiry_date' => 'nullable|date|after_or_equal:to_date',
        'content_type' => 'required|in:video,file',
        'require_proof' => 'boolean',
    ];


    public function mount()
    {
        $this->company_id = app('authUser')->company->id;
        $this->loaded = collect();

        $this->employees = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->orderBy('f_name')
            ->get();


        $this->loadMore();

         if (request()->has('id')) {
         $this->openTrainingId = request('id');


         $this->viewReport($this->openTrainingId);
    }
    }

    public function render()
    {
        return view('livewire.backend.company.training.training-index', [
            'infos' => $this->loaded
        ]);
    }


       public function updatedSendEmail($value)
    {
        if ($value) {
            $gateway = EmailSetting::where('company_id', $this->company_id)->first();

            $this->emailGatewayMissing = $gateway ? false : true;


            if (!$gateway) {
                $this->send_email = false;
            }
        } else {
            $this->emailGatewayMissing = false;
        }
    }


    /* Reset form fields */
    public function resetInputFields()
    {
        $this->training = null;
        $this->training_id = null;
        $this->course_name = '';
        $this->description = '';
        $this->content_type = 'video';
        $this->from_date = null;
        $this->to_date = null;
        $this->expiry_date = null;
        $this->instruction_file = null;
        $this->instruction_text = null;
        $this->require_proof = false;
        $this->emailGatewayMissing = false;
        $this->selectedEmployees = [];

        $this->resetErrorBag();
        $this->resetValidation();
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

    /* Save Training */
    public function saveTraining()
    {
        $this->validate([
            'selectedEmployees' => 'required|array|min:1',

            'course_name' => [
                'required',
                Rule::unique('trainings', 'course_name')
                    ->where('company_id', $this->company_id)
                    ->ignore($this->training_id),
            ],

            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'expiry_date' => 'nullable|date|after_or_equal:to_date',

            'content_type' => 'required|in:video,file',

            'instruction_file' => [
                'file',
                function ($attribute, $value, $fail) {
                    if ($this->content_type === 'video' && !$value) {
                        $fail('The instruction file field is required for video.');
                    } elseif ($this->content_type === 'file' && !$value) {
                        $fail('The instruction file field is required for PDF.');
                    }
                },
                'mimes:mp4,mov,avi,wmv,pdf',
                'max:212000',
            ],

            'require_proof' => 'boolean',
            'send_email' => 'boolean',
        ]);


        if ($this->send_email) {
            $gateway = EmailSetting::where('company_id', $this->company_id)->first();

            if (! $gateway) {
                $this->toast('SMTP gateway not found for this company!', 'error');

                return;
            }
        }



        if ($this->instruction_file) {
            $extension = $this->instruction_file->getClientOriginalExtension();
            $randomName = rand(10000000, 99999999) . now()->format('is') . '.' . $extension;

            $filePath = $this->instruction_file->storeAs('training', $randomName, 'public');
        }




        $training = Training::create([
            'company_id'       => $this->company_id,
            'course_name'      => $this->course_name,
            'description'      => $this->description,
            'content_type'     => $this->content_type,
            'file_path'        => $filePath,
            'from_date'    => $this->from_date !== '' ? $this->from_date : null,
            'to_date'    => $this->to_date !== '' ? $this->to_date : null,
            'expiry_date'    => $this->expiry_date !== '' ? $this->expiry_date : null,
            'required_proof'   => $this->require_proof ?? 0,
            'send_email'       => $this->send_email ?? 0,
        ]);

        foreach ($this->selectedEmployees as $employeeId) {
            foreach ($this->selectedEmployees as $emp) {
                TrainingAssignment::create([
                    'training_id' => $training->id,
                    'user_id'     => $emp['id'],
                    'status'      => 'assigned',
                    'completed_at' => null,
                    'proof_file'   => null,
                ]);
            }


            if ($this->send_email) {
                $user = User::find($emp['id']);
                if ($user && $user->email) {
                    SendTrainingNotification::dispatch($user, $training)->onConnection('sync')->onQueue('urgent');
                }
            }



            $message = "You have been assigned the training '{$training->course_name}'.";

            // Create notification
            $notification = Notification::create([
                'company_id' => $this->company_id,
                'user_id'    => $emp['id'],
                'notifiable_id' => $training ->id,
                'type'       => 'assigned_training',
                'data'       => [
                    'message' => $message
                ],
            ]);

            // Fire real-time event
            event(new NotificationEvent($notification));
        }


        $this->toast('Training created successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }




    public function edit($id)
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->training_id = $id;
        $this->training = Training::with('assignments')->where('company_id', $this->company_id)->find($id);


        $this->course_name = $this->training->course_name;
        $this->description = $this->training->description;
        $this->from_date = $this->training->from_date;
        $this->to_date = $this->training->to_date;
        $this->expiry_date = $this->training->expiry_date;
        $this->content_type = $this->training->content_type;
        $this->instruction_file = $this->training->file_path ?? null;
        $this->require_proof = (bool) $this->training->required_proof;


        $this->selectedEmployees = $this->training->assignments->map(function ($a) {
            return ['id' => $a->user_id, 'name' => $a->user->full_name];
        })->toArray();


        $this->dispatch('load-description-edit', [
            'description' => $this->description,
        ]);
    }




    public function updateTraining()
    {
        $rules = $this->rules;


        if ($this->instruction_file instanceof UploadedFile) {
            $rules['instruction_file'] = $this->content_type === 'video'
                ? 'file|mimes:mp4,mov,avi,wmv|max:212000'
                : 'file|mimes:pdf|max:10480';
        }

        $this->validate($rules);

        $training = Training::findOrFail($this->training_id);


        if ($this->instruction_file instanceof UploadedFile) {

            if ($training->file_path && \Storage::disk('public')->exists($training->file_path)) {
                \Storage::disk('public')->delete($training->file_path);
            }

            // Store new file with random 8-digit name
            $extension = $this->instruction_file->getClientOriginalExtension();
            $randomName = rand(10000000, 99999999) . now()->format('is') . '.' . $extension;
            $filePath = $this->instruction_file->storeAs('training', $randomName, 'public');

            $training->file_path = $filePath;
        }

        $training->update([
            'course_name'    => $this->course_name,
            'description'    => $this->description,
            'from_date'    => $this->from_date !== '' ? $this->from_date : null,
            'to_date'    => $this->to_date !== '' ? $this->to_date : null,
            'expiry_date'    => $this->expiry_date !== '' ? $this->expiry_date : null,
            'content_type'   => $this->content_type,
            'required_proof' => $this->require_proof,
        ]);

        // Update assignments
        $currentIds = $training->assignments->pluck('user_id')->toArray();
        $newIds = collect($this->selectedEmployees)->pluck('id')->toArray();

        // Remove unassigned users
        $toRemove = array_diff($currentIds, $newIds);
        TrainingAssignment::whereIn('user_id', $toRemove)
            ->where('training_id', $training->id)
            ->delete();

        // Add newly assigned users
        $toAdd = array_diff($newIds, $currentIds);
        foreach ($toAdd as $userId) {
            TrainingAssignment::create([
                'training_id' => $training->id,
                'user_id'     => $userId,
                'status'      => 'assigned',
            ]);
        }

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

    public function viewReport($trainingId)
    {
        $this->trainingId = $trainingId;

        $this->training = Training::with(['assignments.user'])
            ->findOrFail($trainingId);

       $this->dispatch('show-training-modal');
    }


    public function sendReminder($id)
    {

        $gateway = EmailSetting::where('company_id', $this->company_id)->first();

        if (! $gateway) {
            $this->toast('SMTP gateway not found for this company!', 'error');

            return;
        }

        SendTrainingReminder::dispatch($id)->onConnection('sync')->onQueue('urgent');

        $this->toast('Reminder sent successfully!', 'success');
    }


    /* Export Training List */
    public function exportTrainings($type)
    {
        $data = $this->loaded->map(function ($t) {
            $totalAssigned = $t->assignments->count();
            $completedCount = $t->assignments->where('status', 'completed')->count();

            return [
                'id'            => $t->id,
                'course_name'   => $t->course_name,
                'content_type'  => $t->content_type,
                'from_date'     => $t->from_date,
                'expiry_date'   => $t->expiry_date,
                'completion'    => "$completedCount / $totalAssigned",
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
                    'Completion',
                    'Created At'
                ],
                'keys' => [
                    'id',
                    'course_name',
                    'content_type',
                    'from_date',
                    'expiry_date',
                    'completion',
                    'created_at'
                ]
            ]
        );
    }
}
