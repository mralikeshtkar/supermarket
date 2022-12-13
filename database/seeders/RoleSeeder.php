<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Permission\Entities\Role;
use Modules\Permission\Enums\Roles;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Roles::asArray() as $role) {
            $role_created = Role::query()->firstOrCreate(['name' => Arr::get($role, 'name_en'),],[
                'name' => Arr::get($role, 'name_en'),
                'name_fa' => Arr::get($role, 'name_fa'),
                'guard_name' => 'sanctum',
            ]);
            $role_created->syncPermissions(Arr::get($role, 'permissions', []));
        }
    }
}
