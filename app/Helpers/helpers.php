<?php

use App\Models\ChatMessage;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\ShiftDate;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;



if (!function_exists('siteSetting')) {
  function siteSetting()
  {

    $settings = SiteSetting::first();

    return $settings;
  }
}


if (!function_exists('getSiteEmail')) {
  function getSiteEmail()
  {
    $settings = siteSetting();
    return $settings?->site_email ?? null;
  }
}


if (!function_exists('statusBadge')) {
  /**
   * Returns HTML badge for status.
   *
   * @param string $status
   * @return string
   */
  function statusBadge($status)
  {
    if ($status === 'Active') {
      return '<span class="badge bg-success">Active</span>';
    } elseif ($status === 'Inactive') {
      return '<span class="badge bg-danger">Inactive</span>';
    } else {
      return '<span class="badge bg-secondary">Unknown</span>';
    }
  }
}


if (!function_exists('statusBadgeTwo')) {
  function statusBadgeTwo($status)
  {
    if ($status == 1) {
      return '<span class="badge bg-success">Active</span>';
    } else {
      return '<span class="badge bg-danger">Former</span>';
    }
  }
}

if (!function_exists('getCopyrightText')) {
  function getCopyrightText()
  {
    $settings = siteSetting();
    return $settings?->copyright_text ?? null;
  }
}

function currentCompanyId()
{
  $user = auth()->user();

  if (!$user) return null;

  if ($user->user_type === 'company') {
    return $user->company->id;
  }

  if ($user->user_type === 'employee' || $user->user_type === 'manager') {
    return $user->employee->company_id;
  }

  return null;
}



class EnvUpdater
{
  /**
   * Update or add key in .env file
   */
  public static function set(array $data)
  {
    $envPath = base_path('.env');

    if (!file_exists($envPath)) return false;

    $env = file_get_contents($envPath);

    foreach ($data as $key => $value) {
      $pattern = "/^{$key}=.*/m";

      if (preg_match($pattern, $env)) {
        $env = preg_replace($pattern, "{$key}={$value}", $env);
      } else {
        $env .= "\n{$key}={$value}";
      }
    }

    file_put_contents($envPath, $env);

    return true;
  }
}



/**
 * Check if an employee has any APPROVED leave on a given date
 *
 * @param int $empId
 * @param string $date  Y-m-d
 * @param int|null $companyId  optional, auto-fetch from emp if null
 * @return bool
 */
function hasLeave(int $empId, string $date, ?int $companyId = null): bool
{
  $emp = Employee::find($empId);

  if (!$emp) {
    return false;
  }

  return LeaveRequest::query()
    ->when(
      $companyId,
      fn($q) => $q->where('company_id', $companyId),
      fn($q) => $q->where('company_id', $emp->company_id)
    )
    ->where('user_id', $emp->user_id)
    ->where('status', 'approved')
    ->whereDate('start_date', '<=', $date)
    ->whereDate('end_date', '>=', $date)
    ->exists();
}



if (!function_exists('todaysShiftForUser')) {
  /**
   * Return today's ShiftDate (with relations) for the authenticated user
   * null if no shift assigned today
   */
  function todaysShiftForUser(?int $userId = null): ?ShiftDate
  {
    $userId = $userId ?: Auth::id();
    if (!$userId) return null;

    return ShiftDate::whereDate('date', Carbon::today())
      ->whereHas('employees', fn($q) => $q->where('user_id', $userId))
      ->with(['shift', 'breaks']) // eager load anything you need
      ->first();
  }



  if (!function_exists('getTrialInfo')) {
    function getTrialInfo($status, $subscription_end)
    {
      if ($status !== 'trial' || !$subscription_end) {
        return null;
      }

      $endDate = Carbon::parse($subscription_end);
      $today = Carbon::today();

      if ($today->greaterThan($endDate)) {
        return "<span class='text-danger fw-bold'>Your trial has ended.</span>";
      }

      $daysLeft = $today->diffInDays($endDate);

      return "You are currently on <span class='text-danger fw-bold'>{$daysLeft}</span> days trial plan.";
    }
  }



  function getGlobalUnreadCount($userId)
  {
    $companyId = currentCompanyId();

    // 1️⃣ Group unread
    $groupUnread = ChatMessage::where('company_id', $companyId)
      ->whereNull('receiver_id')
      ->whereNull('team_id')
      ->where('sender_id', '!=', $userId)
      ->whereDoesntHave('reads', function ($q) use ($userId) {
        $q->where('user_id', $userId)
          ->whereNotNull('read_at');
      })
      ->count();

    // 2️⃣ Team unread
    $teamUnread = ChatMessage::where('company_id', $companyId)
      ->whereNotNull('team_id')
      ->where('sender_id', '!=', $userId)
      ->whereDoesntHave('reads', function ($q) use ($userId) {
        $q->where('user_id', $userId)
          ->whereNotNull('read_at');
      })
      ->count();

    $personalUnread = ChatMessage::where('company_id', $companyId)
      ->where('receiver_id', $userId)
      ->where('is_read', 0)
      ->count();

    return $groupUnread + $teamUnread + $personalUnread;
  }
}
