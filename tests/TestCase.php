<?php

namespace Tests;

use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Address\Database\Seeders\ProvinceCitySeeder;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication,RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
            ProvinceCitySeeder::class,
        ]);
    }
}
