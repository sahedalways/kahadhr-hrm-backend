<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class ThrottleRequests
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @param  int  $maxAttempts 
   * @param  int  $decayMinutes 
   */
  public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
  {
    $key = $this->resolveRequestSignature($request);

    $attempts = Cache::get($key, 0);

    if ($attempts >= $maxAttempts) {
      throw new ThrottleRequestsException('Too many requests. Please try again later.');
    }

    Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));

    return $next($request);
  }

  protected function resolveRequestSignature($request)
  {
    return sha1(
      $request->ip() . '|' . $request->path()
    );
  }
}
