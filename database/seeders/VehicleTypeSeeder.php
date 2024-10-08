<?php

namespace Database\Seeders;

use App\VehicleType;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            'Car',
            'Motor cycle',
        ];
        foreach ($vehicles as  $name) {
            VehicleType::create([
                'name' => $name,
            ]);
        }
    }
}
