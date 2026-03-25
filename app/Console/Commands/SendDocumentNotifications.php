<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\CompanyDocumentSetting;
use App\Models\Notification;
use App\Events\NotificationEvent;
use App\Jobs\EmployeeDocumentNotificationJob;
use Carbon\Carbon;

class SendDocumentNotifications extends Command
{
    protected $signature = 'notify:documents';
    protected $description = 'Send document expiry notifications to employees';

    public function handle()
    {
        $companies = CompanyDocumentSetting::all();

        foreach ($companies as $setting) {
            $companyId = $setting->company_id;
            $notificationType = $setting->notification_type; // system, email, both

            $employees = Employee::with(['documents', 'user', 'company'])
                ->where('company_id', $companyId)
                ->get();

            foreach ($employees as $emp) {
                foreach ($emp->documents as $doc) {
                    $docType = $doc->documentType;

                    if (!$docType || !$emp->user) {
                        continue;
                    }

                    $expiryDate = Carbon::parse($doc->expires_at);
                    $today = Carbon::today();
                    $daysLeft = $today->diffInDays($expiryDate, false);

                    if ($daysLeft < 0) {
                        if ($this->shouldNotify($doc, $setting->notification_frequency, 'expired')) {
                            $message = "{$docType->name} expired. Please upload a new one.";

                            // System notification
                            if ($notificationType === 'system' || $notificationType === 'both') {
                                $notification = Notification::create([
                                    'company_id'    => $companyId,
                                    'user_id'       => $emp->user->id,
                                    'type'          => 'document_expired',
                                    'notifiable_id' => $docType->id,
                                    'data'          => ['message' => $message],
                                ]);

                                event(new NotificationEvent($notification));
                            }


                            if ($notificationType === 'email' || $notificationType === 'both') {
                                EmployeeDocumentNotificationJob::dispatch($emp->id, $docType->id, 'expired', $message);
                            }

                            $doc->last_notified_at = now();
                            $doc->save();
                        }
                    } elseif ($daysLeft <= $setting->doc_expiry_days) {
                        if ($this->shouldNotify($doc, $setting->notification_frequency, 'soon')) {
                            $message = "{$docType->name} expiring soon. Please update it.";

                            if ($notificationType === 'system' || $notificationType === 'both') {
                                $notification = Notification::create([
                                    'company_id'    => $companyId,
                                    'user_id'       => $emp->user->id,
                                    'type'          => 'document_expired',
                                    'notifiable_id' => $docType->id,
                                    'data'          => ['message' => $message],
                                ]);

                                event(new NotificationEvent($notification));
                            }

                            // Email notification
                            if ($notificationType === 'email' || $notificationType === 'both') {
                                EmployeeDocumentNotificationJob::dispatch($emp->id, $docType->id, 'soon', $message);
                            }

                            $doc->last_notified_at = now();
                            $doc->save();
                        }
                    }
                }
            }
        }

        $this->info('Document notifications dispatched successfully!');
    }

    /**
     * Determine if we should notify based on frequency
     */
    protected function shouldNotify($doc, $frequencyDays, $type)
    {
        $lastNotified = $doc->last_notified_at;
        if (!$lastNotified) {
            return true;
        }

        return now()->diffInDays($lastNotified) >= $frequencyDays;
    }
}
