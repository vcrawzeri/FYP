<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usaStates = [
            "CO" => 'Colorado',
            "FL" => 'Florida',
            "GA" => 'Georgia',
            "HI" => 'Hawaii',
            "IL" => 'Illinois',
        ];

        $countries = [
            ['code' => 'egy', 'name' => 'Egypt', 'states' => null],
            ['code' => 'ind', 'name' => 'India', 'states' => null],
            ['code' => 'usa', 'name' => 'United States of America', 'states' => json_encode($usaStates)],
            ['code' => 'sau', 'name' => 'Saudi Arabia', 'states' => null],
        ];
        Country::insert($countries);
    }
}


