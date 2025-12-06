<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTrainingNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $training;

    public function __construct(User $user, Training $training)
    {
        $this->user = $user;
        $this->training = $training;
    }
    public function handle()
    {
        try {
            Mail::send('mail.training_notification', [
                'training' => $this->training,
                'user' => $this->user,
                'isReminder' => false,
            ], function ($message) {
                $message->to($this->user->email, $this->user->full_name)
                    ->subject('New Training Assigned: ' . $this->training->course_name);
            });
        } catch (\Exception $e) {

            Log::error("Training Notification email failed for user {$this->user->email}: " . $e->getMessage());
        }
    }


    public function failed(\Exception $exception)
    {
        Log::error("Training Notification job failed for user {$this->user->email}: " . $exception->getMessage());
    }
}
