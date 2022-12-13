<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Permission\Entities\Permission;
use Modules\Permission\Enums\Permissions;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Permissions::asArray() as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
            ],[
                'name' => $permission,
                'guard_name' => 'sanctum',
            ]);
        }
    }
}
