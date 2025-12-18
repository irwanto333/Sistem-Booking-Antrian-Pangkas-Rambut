<?php

namespace Database\Seeders;

use App\Models\TukangCukur;
use Illuminate\Database\Seeder;

class TukangCukurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tukangCukurs = [
            [
                'name' => 'Pak Budi',
                'phone' => '081234567890',
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Mas Andi',
                'phone' => '081234567891',
                'photo' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Bang Dedi',
                'phone' => '081234567892',
                'photo' => null,
                'is_active' => true,
            ],
        ];

        foreach ($tukangCukurs as $tukangCukur) {
            TukangCukur::create($tukangCukur);
        }
    }
}
