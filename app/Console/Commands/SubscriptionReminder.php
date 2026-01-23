<?php

namespace App\Console\Commands;

use App\Jobs\PaymentStatusEmailJob;
use App\Models\Company;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SubscriptionReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:subscription-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to add card before subscription expires';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysBeforeList = [7, 3, 1, 0];

        foreach ($daysBeforeList as $daysBefore) {

            $targetDate = Carbon::today()->addDays($daysBefore);

            $companies = Company::where('subscription_status', 'active')
                ->whereDate('subscription_end', $targetDate)
                ->get();

            foreach ($companies as $company) {

                // Skip if card already exists
                if ($company->hasValidCard()) {
                    continue;
                }

                // Prevent duplicate notification same day
                $alreadySent = Notification::where('company_id', $company->id)
                    ->where('type', 'card_reminder')
                    ->whereDate('created_at', Carbon::today())
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                Notification::create([
                    'company_id' => $company->id,
                    'user_id' => $company->user_id,
                    'type' => 'card_reminder',
                    'data' => json_encode([
                        'days_left' => $daysBefore,
                        'message' => $this->messageText($daysBefore),
                    ]),
                ]);

                PaymentStatusEmailJob::dispatch($company->id, 'card_reminder');

                $this->info("Reminder sent to {$company->company_name} ({$daysBefore} days left)");
            }
        }
    }

    protected function messageText($days)
    {
        if ($days == 0) {
            return 'Your subscription expires today. Please add a card to avoid service interruption.';
        }

        return "Your subscription will expire in {$days} day(s). Please add your card to continue uninterrupted service.";
    }
}
