<?php

namespace App\Jobs;

use App\Models\TrainingAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTrainingReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $assignmentId;

    /**
     * Create a new job instance.
     */
    public function __construct($assignmentId)
    {
        $this->assignmentId = $assignmentId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $assignment = TrainingAssignment::with('user', 'training')->find($this->assignmentId);

        if (!$assignment || $assignment->status === 'completed') {
            return;
        }

        try {
            Mail::send('mail.training_notification', [
                'training' => $assignment->training,
                'user' => $assignment->user,
                'isReminder' => true,
            ], function ($message) use ($assignment) {
                $message->to($assignment->user->email, $assignment->user->full_name)
                    ->subject('Reminder: Complete your assigned training: ' . $assignment->training->course_name);
            });
        } catch (\Exception $e) {
            Log::error('Failed to send reminder email for assignment ID ' . $this->assignmentId . ': ' . $e->getMessage());
        }
    }
}
