<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeaveType;

class LeaveTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            [
                'name'  => 'Annual Leave',
                'emoji' => '🏖️',
            ],
            [
                'name'  => 'Sickness',
                'emoji' => '🩺',
            ],
            [
                'name'  => 'Maternity/Paternity',
                'emoji' => '🧸',
            ],
            [
                'name'  => 'Unpaid',
                'emoji' => '✈️',
            ],
            [
                'name'  => 'Carry Forward',
                'emoji' => '🤝🏻',
            ],
            [
                'name'  => 'Others',
                'emoji' => '📍',
            ],
        ];

        foreach ($types as $type) {
            LeaveType::updateOrCreate(
                ['name' => $type['name']],
                ['emoji' => $type['emoji']]
            );
        }
    }
}
