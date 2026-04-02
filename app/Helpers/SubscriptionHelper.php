<?php

namespace App\Helpers;

use Carbon\Carbon;

class SubscriptionHelper
{
    /**
     * Get subscription status with formatted message
     *
     * @param string $status - subscription status (active/trial/expired/suspended)
     * @param string|null $trial_end_date - trial end date
     * @param string|null $subscription_start_date - subscription start date (when payment taken)
     * @param string|null $subscription_end_date - subscription end date (next billing date)
     * @return array - ['html' => 'formatted message', 'days_left' => days remaining]
     */
    public static function getSubscriptionInfo($status, $trial_end_date = null, $subscription_start_date = null, $subscription_end_date = null)
    {


        switch ($status) {
            case 'trial':
                return self::getTrialInfo($trial_end_date);

            case 'active':
                return self::getActiveInfo($subscription_end_date);

            case 'expired':
                return self::getExpiredInfo();

            case 'suspended':
                return self::getSuspendedInfo();

            default:
                return [
                    'html' => '<span class="text-muted">No subscription information available</span>',
                    'days_left' => 0
                ];
        }
    }

    /**
     * Get trial information
     */
    private static function getTrialInfo($trial_end_date)
    {
        if (!$trial_end_date) {
            return [
                'html' => '<span class="text-muted">No trial information available</span>',
                'days_left' => 0
            ];
        }

        $endDate = Carbon::parse($trial_end_date);
        $today = Carbon::now();

        if ($today->greaterThan($endDate)) {
            return [
                'html' => '<span class="text-danger fw-bold">⚠️ Your trial has ended. Please subscribe to continue.</span>',
                'days_left' => 0
            ];
        }


        $daysLeft = (int) ceil($today->diffInDays($endDate, false));


        return [
            'html' => '<div class="trial-info">
                        <span class="text-warning fw-bold">🎉 Trial Mode</span><br>
                        <small>' . $daysLeft . ' days remaining in trial</small>
                    </div>',
            'days_left' => $daysLeft
        ];
    }

    /**
     * Get active subscription information
     */
    private static function getActiveInfo($subscription_end_date)
    {
        if (!$subscription_end_date) {
            return [
                'html' => '<span class="text-success fw-bold">✅ Subscription Active</span>',
                'days_left' => 0
            ];
        }

        $nextBillingDate = Carbon::parse($subscription_end_date);
        $today = Carbon::now();

        // Check if subscription has expired
        if ($today->greaterThan($nextBillingDate)) {
            return [
                'html' => '<span class="text-danger fw-bold">⚠️ Subscription expired. Please renew.</span>',
                'days_left' => 0
            ];
        }

        $daysLeft = (int) ceil($today->diffInDays($nextBillingDate, false));

        // Format date for display
        $formattedDate = $nextBillingDate->format('d M, Y');

        return [
            'html' => '<div class="active-info">
                            <span class="text-success fw-bold">✅ Active Plan</span><br>
                            <small class="text-muted">Next payment: ' . $formattedDate . '</small><br>
                            <small class="text-info">' . $daysLeft . ' days remaining</small>
                        </div>',
            'days_left' => $daysLeft
        ];
    }

    /**
     * Get expired information
     */
    private static function getExpiredInfo()
    {
        return [
            'html' => '<div class="expired-info">
                            <span class="text-danger fw-bold">❌ Subscription Expired</span><br>
                            <small>Please renew your subscription to continue using our services.</small>
                        </div>',
            'days_left' => 0
        ];
    }

    /**
     * Get suspended information
     */
    private static function getSuspendedInfo()
    {
        return [
            'html' => '<div class="suspended-info">
                            <span class="text-danger fw-bold">⛔ Account Suspended</span><br>
                            <small>Please contact support to resolve the issue.</small>
                        </div>',
            'days_left' => 0
        ];
    }

    /**
     * Get formatted days remaining text (for simple display)
     */
    public static function getDaysRemainingText($status, $end_date = null)
    {
        $info = self::getSubscriptionInfo($status, $end_date);

        if ($info['days_left'] > 0) {
            return $info['days_left'] . ' days remaining';
        } elseif ($status === 'trial' && $info['days_left'] === 0) {
            return 'Trial ended';
        } elseif ($status === 'active' && $info['days_left'] === 0) {
            return 'Payment due';
        }

        return 'N/A';
    }
}
