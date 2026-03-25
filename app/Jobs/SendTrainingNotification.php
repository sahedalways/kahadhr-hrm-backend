<?php

namespace App\Jobs;

use App\Models\EmailSetting;
use App\Models\Employee;
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

    protected int $userId;
    protected int $trainingId;

    public function __construct(int $userId, int $trainingId)
    {
        $this->userId = $userId;
        $this->trainingId = $trainingId;
    }

    public function handle()
    {
        $user = Employee::find($this->userId);
        $training = Training::find($this->trainingId);

        if (!$user || !$training) return;

        try {
            $gateway = EmailSetting::where('company_id', $user->company_id)->first();
            configureSmtp($gateway);

            Mail::send('mail.training_notification', [
                'training' => $training,
                'user' => $user,
                'isReminder' => false,
            ], function ($message) use ($user, $training) {
                $message->to($user->email, $user->full_name)
                    ->subject('New Training Assigned: ' . $training->course_name);
            });
        } catch (\Exception $e) {
            Log::error("Training Notification email failed for user {$user->email}: " . $e->getMessage());
        }
    }
}
