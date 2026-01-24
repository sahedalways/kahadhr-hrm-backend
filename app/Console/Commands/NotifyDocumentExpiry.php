<?php

namespace App\Console\Commands;

use App\Events\NotificationEvent;
use Illuminate\Console\Command;
use App\Models\EmpDocument;

use App\Models\Notification;
use Carbon\Carbon;

class NotifyDocumentExpiry extends Command
{
    protected $signature = 'notify:document-expiry';
    protected $description = 'Notify employees about document expiry';

    public function handle()
    {
        $today = Carbon::now();
        $cutoff = Carbon::now()->addDays(30);


        $docs = EmpDocument::with(['employee.user', 'documentType'])
            ->whereNotNull('expires_at')
            ->where(function ($q) use ($today, $cutoff) {
                $q->whereDate('expires_at', '<', $today)
                    ->orWhereBetween('expires_at', [$today, $cutoff]);
            })
            ->get();

        foreach ($docs as $doc) {

            $docType = $doc->documentType;
            $emp = $doc->employee;

            // Determine type
            if ($doc->expires_at->isPast()) {
                $type = 'expired';
                $message = "{$docType->name} expired. Please upload a new one.";
            } else {
                $type = 'soon';
                $message = "{$docType->name} expiring soon. Please update it.";
            }

            // Save Notification
            $notification = Notification::create([
                'company_id'    => $emp->company_id,
                'user_id'       => $emp->user->id,
                'type'          => 'document_expired',
                'notifiable_id' => $docType->id,
                'data'          => [
                    'message' => $message,

                ],
            ]);

            event(new NotificationEvent($notification));
        }

        $this->info('Document expiry notification process completed.');
    }
}
