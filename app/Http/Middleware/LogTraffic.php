<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stevebauman\Location\Facades\Location;

class LogTraffic
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('api/*')) {
            $ip = request()->ip();
            $position = Location::get($ip);

            $region = $position ? $position->regionName : 'Unknown Region';

            // Example for logging
            DB::table('traffic_logs')->insert([
                'endpoint' => $request->path(),
                'region' => $region,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $next($request);
    }
}
