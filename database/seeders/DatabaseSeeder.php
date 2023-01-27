<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Address\Database\Seeders\ProvinceCitySeeder;
use Modules\Permission\Entities\Permission;
use Modules\Permission\Entities\Role;
use Modules\User\Entities\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            ProvinceCitySeeder::class,
            UserSeeder::class,
            //ProductSeeder::class,
            //AddressSeeder::class,
        ]);
    }
}
