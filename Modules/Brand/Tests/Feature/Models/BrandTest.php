<?php

namespace Modules\Brand\Tests\Feature\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Modules\Brand\Entities\Brand;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BrandTest extends TestCase
{
    /**
     * Check init method rerun an instance of it class.
     *
     * @return void
     */
    public function testInitializeClass()
    {
        $this->assertInstanceOf(Brand::class, Brand::init());
    }

    /**
     * Store a brand to database using store method.
     *
     * @return void
     */
    public function testStoreBrandToDatabase()
    {
        $this->assertDatabaseCount(Brand::class, 0);
        $request = Request::capture();
        $request->merge([
            'name' => $this->faker->unique()->name,
            'name_en' => $this->faker->unique()->name,
            'slug' => $this->faker->unique()->slug,
        ]);
        Brand::init()->store($request);
        $this->assertDatabaseCount(Brand::class, 1);
    }

    /**
     * Find a brand by column name.
     *
     * @return void
     */
    public function testFindBrandByColumnName()
    {
        Brand::factory()->create();
        $this->assertNotNull(Brand::init()->findByColumnOrFail(1));
    }

    /**
     * Throw Model not found exception when find brand by column name have invalid data;
     *
     * @return void
     */
    public function testThrowExceptionWhenFindBrandByColumnHaveInvalidValue()
    {
        Brand::factory()->create();
        $this->expectException(ModelNotFoundException::class);
        Brand::init()->findByColumnOrFail(10);
    }
}
