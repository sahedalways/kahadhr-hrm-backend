<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BookingInfo;
use Carbon\Carbon;

class CancelPendingBookings extends Command
{
    protected $signature = 'bookings:cancel-pending';
    protected $description = 'Cancel pending bookings that are older than 2 minutes';

    public function handle()
    {
        $cutoffTime = Carbon::now()->subMinutes(2);

        $bookings = BookingInfo::where('status', 'pending')
            ->where('created_at', '<=', $cutoffTime)
            ->orWhere('updated_at', '<=', $cutoffTime)
            ->get();

        foreach ($bookings as $booking) {
            $booking->status = 'cancelled';
            $booking->save();
            $this->info("Booking ID {$booking->id} cancelled.");
        }

        $this->info('Pending bookings check completed.');
        return 0;
    }
}
