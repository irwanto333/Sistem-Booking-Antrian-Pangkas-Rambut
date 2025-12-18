<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\TukangCukur;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tukangCukurs = TukangCukur::all();

        // Default schedule: Monday to Saturday, 08:00 - 20:00
        // Sunday: 09:00 - 17:00
        // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        $schedules = [
            1 => ['open' => '08:00', 'close' => '20:00', 'available' => true], // Monday
            2 => ['open' => '08:00', 'close' => '20:00', 'available' => true], // Tuesday
            3 => ['open' => '08:00', 'close' => '20:00', 'available' => true], // Wednesday
            4 => ['open' => '08:00', 'close' => '20:00', 'available' => true], // Thursday
            5 => ['open' => '08:00', 'close' => '20:00', 'available' => true], // Friday
            6 => ['open' => '08:00', 'close' => '20:00', 'available' => true], // Saturday
            0 => ['open' => '09:00', 'close' => '17:00', 'available' => true], // Sunday
        ];

        foreach ($tukangCukurs as $tukangCukur) {
            foreach ($schedules as $dayOfWeek => $time) {
                Schedule::create([
                    'tukang_cukur_id' => $tukangCukur->id,
                    'day_of_week' => $dayOfWeek,
                    'open_time' => $time['open'],
                    'close_time' => $time['close'],
                    'is_available' => $time['available'],
                ]);
            }
        }
    }
}
