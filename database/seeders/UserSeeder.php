<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Permission\Enums\Roles;
use Modules\User\Entities\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(30)->create();
        $this->_createSuperAdmin();
    }

    /**
     * @return void
     */
    private function _createSuperAdmin(): void
    {
        $user = User::query()->firstOrCreate([
            'mobile' => "+989123456789",
        ],[
            'mobile' => "+989123456789",
            'email' => "admin@example.com",
            'name' => "admin",
            'password' => bcrypt('admin'),
        ]);
        file_put_contents(storage_path('logs/laravel.log'),'');
        logger($user->createToken('auth_token')->plainTextToken);
        $user->assignRole(Roles::SUPER_ADMIN['name_en']);
    }
}
