<?php

namespace Modules\Address\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Modules\Address\Entities\City;
use Modules\Address\Entities\Province;

class ProvinceCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = json_decode(file_get_contents(storage_path('cities.json')), true);
        foreach ($provinces as $province) {
            $provinceItem = Province::query()
                ->firstOrCreate(['name' => $province['name']], ['name' => $province['name']]);
            foreach ($province['cities'] as $city) {
                $provinceItem->cities()->firstOrCreate(['name' => $city], ['name' => $city]);
            }
        }
    }
}
