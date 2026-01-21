<?php

namespace App\Livewire\Backend\Employee\Training;

use App\Events\NotificationEvent;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Notification;
use App\Models\Training;
use App\Traits\Exportable;
use Livewire\WithPagination;
use Carbon\Carbon;
use Livewire\WithFileUploads;

class TrainingIndexEmp extends BaseComponent
{
    use WithPagination, Exportable;
    use WithFileUploads;

    public $search = '';
    public $sortOrder = 'desc';
    public $perPage = 10;

    public $loaded;
    public $lastId = null;
    public $hasMore = true;

    public $training;
    public $assignment;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['markCompleted : markCompleted'];
    public $proofFile;

    public function mount()
    {
        $this->loaded = collect();
        $this->loadMore();
    }

    public function updatingSearch()
    {
        $this->resetLoaded();
    }

    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Training::with(['assignments' => fn($q) => $q->where('user_id', auth()->id())])
            ->whereHas('assignments', fn($q) => $q->where('user_id', auth()->id()));

        if ($this->search) {
            $query->where(
                fn($q) =>
                $q->where('course_name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
            );
        }

        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->orderBy('id', $this->sortOrder)->limit($this->perPage)->get();

        if ($items->isEmpty()) {
            $this->hasMore = false;
            return;
        }

        $this->lastId = $this->sortOrder === 'desc' ? $items->last()->id : $items->first()->id;
        $this->loaded = $this->loaded->merge($items);

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }
    }

    private function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }



    public function viewReport($id)
    {
        $this->training = Training::with(['assignments' => fn($q) => $q->where('user_id', auth()->id())])->find($id);
        $this->assignment = $this->training->assignments->first();
    }



    public function markCompleted()
    {
        if ($this->assignment && $this->assignment->status !== 'completed') {


            if ($this->training->required_proof) {
                $this->dispatch('closemodal');
                $this->dispatch('showProofModal');
            } else {

                $this->assignment->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);


                $submitterName = auth()->user()->full_name;
                $trainingName = $this->training->course_name ?? 'Training';

                $message = "Employee '{$submitterName}' has completed the training '{$trainingName}'.";

                $notification = Notification::create([
                    'company_id' => auth()->user()->employee->company_id,
                    'user_id' => null,
                    'type' => 'training_completed',

                    'data' => [
                        'message' => $message

                    ],
                ]);


                event(new NotificationEvent($notification));


                $this->dispatch('closemodal');
                $this->toast('Training marked as completed!', 'success');
            }
        }
    }




    public function submitProof()
    {
        $this->validate([
            'proofFile' => 'required|file|mimes:pdf,jpeg,png,jpg|max:5240',
        ]);

        $filePath = $this->proofFile->store('training/proof_files', 'public');

        $this->assignment->proof_file = $filePath;
        $this->assignment->status = 'completed';
        $this->assignment->completed_at = now();

        $this->assignment->save();


        $submitterName = auth()->user()->full_name;
        $trainingName = $this->training->course_name ?? 'Training';

        $message = "Employee '{$submitterName}' has completed the training '{$trainingName}'.";

        $notification = Notification::create([
            'company_id' => auth()->user()->employee->company_id,
            'user_id' => null,
            'type' => 'training_completed',

            'data' => [
                'message' => $message

            ],
        ]);


        event(new NotificationEvent($notification));

        $this->reset('proofFile');
        $this->dispatch('closemodal');
        $this->toast('Training marked as completed!', 'success');
    }



    public function exportTrainings($type)
    {
        $data = $this->loaded->map(function ($training) {
            $assignment = $training->assignments->first();
            return [
                'id' => $training->id,
                'course_name' => $training->course_name,
                'content_type' => ucfirst($training->content_type),
                'from_date' => $training->from_date,
                'to_date' => $training->to_date,
                'expiry_date' => $training->expiry_date,
                'status' => $assignment?->status ?? 'Pending',
                'proof_file' => $assignment?->proof_file ? 'Yes' : 'No',
                'created_at' => Carbon::parse($training->created_at)->format('d F, Y'),
            ];
        });

        return $this->export(
            $data,
            $type,
            'employee-trainings',
            'exports.generic-table-pdf',
            [
                'title' => siteSetting()->site_title . ' - Assigned Trainings',
                'columns' => [
                    'ID',
                    'Course Name',
                    'Type',
                    'From Date',
                    'To Date',
                    'Expiry Date',
                    'Status',
                    'Proof Submitted',
                    'Created At'
                ],
                'keys' => [
                    'id',
                    'course_name',
                    'content_type',
                    'from_date',
                    'to_date',
                    'expiry_date',
                    'status',
                    'proof_file',
                    'created_at'
                ]
            ]
        );
    }

    public function render()
    {
        return view('livewire.backend.employee.training.training-index-emp', [
            'infos' => $this->loaded
        ]);
    }
}
