<?php

namespace Database\Seeders;

use App\Models\City;
use Cache;
use App\Traits\Common;
use DB;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    use Common;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            'Mumbai',
            'Delhi',
            'Bangalore',
            'Kolkata',
            'Chennai',
            'Hyderabad',
            'Pune',
            'Ahmedabad',
        ];

        Cache::forget('city_list');

        $this->clearEventListCache();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        City::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($cities as $cityName) {
            City::create(['name' => $cityName]);
        }
    }
}
