<?php

use Carbon\Carbon;

$year = Carbon::now()->year;

return [
    $year => [
        'UK' => [
            "$year-01-01" => "New Year's Day",
            "$year-04-06" => "Easter Monday",
            Carbon::create($year, 4, 3)->toDateString() => "Good Friday",
            Carbon::create($year, 5, 4)->toDateString() => "Early May Bank Holiday",
            Carbon::create($year, 5, 25)->toDateString() => "Spring Bank Holiday",
            Carbon::create($year, 8, 31)->toDateString() => "Summer Bank Holiday",
            Carbon::create($year, 12, 25)->toDateString() => "Christmas Day",
            Carbon::create($year, 12, 28)->toDateString() => "Boxing Day (Substitute)",
        ],

    ],
];
