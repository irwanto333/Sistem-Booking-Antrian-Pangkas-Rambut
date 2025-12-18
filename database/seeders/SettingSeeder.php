<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'shop_name',
                'value' => 'Pangkas Rambut Jaya',
            ],
            [
                'key' => 'shop_address',
                'value' => 'Jl. Contoh No. 123, Kota',
            ],
            [
                'key' => 'shop_phone',
                'value' => '021-1234567',
            ],
            [
                'key' => 'booking_time_slot',
                'value' => '30', // minutes
            ],
            [
                'key' => 'max_advance_booking_days',
                'value' => '7', // days
            ],
            [
                'key' => 'allow_cancel_before_minutes',
                'value' => '60', // minutes
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
