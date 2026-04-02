<?php

use App\Models\ChatMessage;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\ShiftDate;
use App\Models\SiteSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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






  /**
   * Verify reCAPTCHA token with Google
   */
  function verifyRecaptcha($token)
  {
    try {
      $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        'secret' => config('services.recaptcha.secret_key'),
        'response' => $token
      ]);

      $body = $response->json();


      if (isset($body['success']) && $body['success'] === true) {
        return true;
      }

      Log::warning('reCAPTCHA verification failed', $body);
      return false;
    } catch (\Exception $e) {
      Log::error('reCAPTCHA verification error: ' . $e->getMessage());
      return false;
    }
  }


  function maskPhone($phone)
  {
    if (!$phone) return '';

    $start = substr($phone, 0, 4);
    $end   = substr($phone, -2);

    return $start . str_repeat('*', strlen($phone) - 6) . $end;
  }


  function maskEmail($email)
  {
    if (!$email) return '';

    [$name, $domain] = explode('@', $email);

    $visible = substr($name, 0, 1);

    return $visible . str_repeat('*', max(strlen($name) - 1, 1)) . '@' . $domain;
  }
}





if (!function_exists('parseTimeToMinutes')) {
  function parseTimeToMinutes($time): int
  {
    if (empty($time)) {
      return 0;
    }

    $time = trim($time);

    if (preg_match('/^(\d{1,2}):(\d{2})$/', $time, $matches)) {
      return (int)$matches[1] * 60 + (int)$matches[2];
    }


    if (is_numeric($time)) {
      $val = (float)$time;
      $hours = floor($val);

      $fraction = $val - $hours;
      $minutes = round($fraction * 100);

      return (int)($hours * 60 + $minutes);
    }

    $minutes = 0;

    if (preg_match('/(\d+)\s*h/i', $time, $matches)) {
      $minutes += (int)$matches[1] * 60;
    }

    if (preg_match('/(\d+)\s*m/i', $time, $matches)) {
      $minutes += (int)$matches[1];
    }

    return $minutes;
  }
}
if (!function_exists('getShiftHours')) {
  function getShiftHours($attendance): array
  {
    $clockIn = $attendance->clock_in instanceof Carbon
      ? $attendance->clock_in
      : ($attendance->clock_in ? Carbon::parse($attendance->clock_in) : null);

    $clockOut = $attendance->clock_out instanceof Carbon
      ? $attendance->clock_out
      : ($attendance->clock_out ? Carbon::parse($attendance->clock_out) : null);

    if (!$clockIn) {
      return [
        'shift_hours'           => '0h 0m',
        'worked_hours'          => '---',
        'total_break_hours'     => '0h 0m',
        'paid_break_hours'      => '0h 0m',
        'unpaid_break_hours'    => '0h 0m',
        'actual_worked_hours'   => '---'
      ];
    }

    $date = $clockIn->format('Y-m-d');
    $employeeId = $attendance->user->employee->id;

    $shiftDate = ShiftDate::where('date', $date)
      ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId))
      ->first();

    $shiftHoursFormatted = '0h 0m';
    $shiftTotalMinutes = 0;

    if ($shiftDate && $shiftDate->total_hours) {
      $shiftTotalMinutes = parseTimeToMinutes($shiftDate->total_hours);
      $shiftHoursFormatted = formatMinutesToHours($shiftTotalMinutes);
    } else {
      $shiftTotalMinutes = 480;
      $shiftHoursFormatted = '0h 0m';
    }

    $paidBreakMinutes = 0;
    $unpaidBreakMinutes = 0;
    $totalBreakMinutes = 0;

    if ($shiftDate && method_exists($shiftDate, 'breaks') && $shiftDate->breaks) {
      foreach ($shiftDate->breaks as $break) {
        if ($break->duration) {
          $breakMinutes = parseTimeToMinutes($break->duration);

          if (isset($break->type) && strtolower($break->type) === 'unpaid') {
            $unpaidBreakMinutes += $breakMinutes;
          } else {
            $paidBreakMinutes += $breakMinutes;
          }

          $totalBreakMinutes += $breakMinutes;
        }
      }
    }

    if (!$clockOut) {
      return [
        'shift_hours'           => $shiftHoursFormatted,
        'worked_hours'          => '0h 0m',
        'total_break_hours'     => formatMinutesToHours($totalBreakMinutes), // ✅
        'paid_break_hours'      => formatMinutesToHours($paidBreakMinutes),
        'unpaid_break_hours'    => formatMinutesToHours($unpaidBreakMinutes),
        'actual_worked_hours'   => '0h 0m'
      ];
    }

    if ($clockOut->lessThan($clockIn)) {
      $clockOut->addDay();
    }

    $totalWorkedMinutes = (int) round($clockIn->diffInRealMinutes($clockOut));

    $actualWorkedMinutes = $totalWorkedMinutes - $unpaidBreakMinutes;

    if ($actualWorkedMinutes < 0) {
      $actualWorkedMinutes = 0;
    }

    return [
      'shift_hours' => $shiftHoursFormatted == '0h 0m'
        ? formatMinutesToHours($totalWorkedMinutes)
        : $shiftHoursFormatted,
      'worked_hours'          => formatMinutesToHours($totalWorkedMinutes),
      'total_break_hours'     => formatMinutesToHours($totalBreakMinutes),
      'paid_break_hours'      => formatMinutesToHours($paidBreakMinutes),
      'unpaid_break_hours'    => formatMinutesToHours($unpaidBreakMinutes),
      'actual_worked_hours'   => formatMinutesToHours($actualWorkedMinutes)
    ];
  }
}



if (!function_exists('formatMinutesToHours')) {
  function formatMinutesToHours(int $minutes): string
  {
    $hours = intdiv($minutes, 60);
    $mins = $minutes % 60;
    return sprintf('%dh %dm', $hours, $mins);
  }
}
