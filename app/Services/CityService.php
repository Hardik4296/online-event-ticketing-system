<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Support\Facades\Cache;

class CityService
{
    protected $city;

    public function __construct()
    {
        $this->city = new City();
    }

    /**
     * Handle get city data
     */
    public function getCityData()
    {
        $cacheKey = 'city_list';

        return Cache::remember($cacheKey, 86400, function () {
            return $this->city->select('cities.id', 'cities.name')->get();
        });
    }
}
