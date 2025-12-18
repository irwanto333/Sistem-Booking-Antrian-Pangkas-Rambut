<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Potong Rambut Standard',
                'description' => 'Potong rambut standar dengan berbagai gaya pilihan',
                'price' => 25000,
                'duration_minutes' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Potong Rambut Premium',
                'description' => 'Potong rambut premium dengan styling dan cuci rambut',
                'price' => 40000,
                'duration_minutes' => 45,
                'is_active' => true,
            ],
            [
                'name' => 'Cukur Kumis & Jenggot',
                'description' => 'Cukur dan rapikan kumis serta jenggot',
                'price' => 15000,
                'duration_minutes' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Hair Coloring',
                'description' => 'Pewarnaan rambut dengan berbagai pilihan warna',
                'price' => 100000,
                'duration_minutes' => 90,
                'is_active' => true,
            ],
            [
                'name' => 'Creambath',
                'description' => 'Perawatan rambut dengan creambath dan pijat kepala',
                'price' => 50000,
                'duration_minutes' => 45,
                'is_active' => true,
            ],
            [
                'name' => 'Paket Komplit',
                'description' => 'Potong rambut + cukur kumis/jenggot + cuci rambut',
                'price' => 50000,
                'duration_minutes' => 60,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
