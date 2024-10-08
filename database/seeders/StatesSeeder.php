<?php

namespace Database\Seeders;

use App\State;
use Illuminate\Database\Seeder;

class StatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $australia_states = [
            'NSW' => 'New South Wales',
            'QLD' => 'Queensland',
            'SA' => 'South Australia',
            'TAS' => 'Tasmania',
            'VIC' => 'Victoria',
            'WA' => 'Western Australia',
            'ACT' => 'Australian Capital Territory',
            'NT' => 'Northern Territory',
        ];
        foreach ($australia_states as $code => $name) {
            State::create([
                'name' => $name,
                'code' => $code,
            ]);
        }
    }
}
