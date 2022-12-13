<?php

namespace Modules\Brand\Tests\Feature\Controllers\V1\Api;

use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Address\Database\Seeders\ProvinceCitySeeder;
use Modules\Brand\Entities\Brand;
use Modules\Media\Entities\Media;
use Modules\Permission\Enums\Permissions;
use Modules\Permission\Enums\Roles;
use Modules\User\Entities\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiBrandControllerTest extends TestCase
{
    /**
     * Permitted user can store brand.
     *
     * @return void
     */
    public function testPermittedUserCanStoreBrandPost()
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create())->assertAuthenticated();
        auth()->user()->assignRole(Roles::SUPER_ADMIN['name_en']);
        $this->_storeData();

        $this->actingAs(User::factory()->create())->assertAuthenticated();
        auth()->user()->givePermissionTo(Permissions::MANAGE_BRANDS);
        $this->_storeData(1);
    }

    public function testPermittedUserCanNotStoreBrandWithInvalidDataPost()
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create())->assertAuthenticated();
        auth()->user()->assignRole(Roles::SUPER_ADMIN['name_en']);
        $this->postJson(route('brand.v1.api-brand.store.post.api'), [
            'image' => UploadedFile::fake()->image('brand.mp4'),
        ])->assertUnprocessable()
            ->assertInvalid(['name', 'name_en', 'slug', 'image']);
    }

    /**
     * Guest or Normal user can not store brand.
     *
     * @return void
     */
    public function testGuestOrNormalUserCanNotStoreBrandPost()
    {
        $this->postJson(route('brand.v1.api-brand.store.post.api'), [
            'name' => $this->faker->name,
            'name_en' => $this->faker->name,
            'slug' => $this->faker->slug,
            'image' => UploadedFile::fake()->image('brand.jpg'),
        ])->assertUnauthorized();
        $this->actingAs(User::factory()->create())->assertAuthenticated();
        $this->postJson(route('brand.v1.api-brand.store.post.api'), [
            'name' => $this->faker->name,
            'name_en' => $this->faker->name,
            'slug' => $this->faker->slug,
            'image' => UploadedFile::fake()->image('brand.jpg'),
        ])->assertForbidden();
    }

    /**
     * Permitted user can update a brand.
     *
     * @return void
     */
    public function testPermittedUserCanUpdateBrandPutPatch()
    {
        $this->actingAs(User::factory()->create())->assertAuthenticated();
        auth()->user()->assignRole(Roles::SUPER_ADMIN['name_en']);
        $this->_updateData();

        $this->actingAs(User::factory()->create())->assertAuthenticated();
        auth()->user()->givePermissionTo(Permissions::MANAGE_BRANDS);
        $this->_updateData();
    }

    /**
     * Permitted user can not update a brand with invalid data.
     *
     * @return void
     */
    public function testPermittedUserCanNotUpdateBrandWithInvalidDataPutPatch()
    {
        $this->actingAs(User::factory()->create())->assertAuthenticated();
        auth()->user()->assignRole(Roles::SUPER_ADMIN['name_en']);
        $brand = Brand::factory()->create();
        $this->patchJson(route('brand.v1.api-brand.update.put-patch.api', $brand->slug), [
            'image' => UploadedFile::fake()->create('brand.mp4'),
        ])->assertUnprocessable()->assertInvalid([
            'name',
            'name_en',
            'slug',
            'image',
        ]);
    }

    /**
     * Guest or Normal user can not update a brand.
     *
     * @return void
     */
    public function testGuestOrNormalUserCanNotUpdateBrandPutPatch()
    {
        $brand = Brand::factory()->create();
        $this->patchJson(route('brand.v1.api-brand.update.put-patch.api', $brand->slug), [
            'name' => $this->faker->name,
            'name_en' => $this->faker->name,
            'slug' => $this->faker->slug,
        ])->assertUnauthorized();

        $this->actingAs(User::factory()->create())->assertAuthenticated();
        $brand = Brand::factory()->create();
        $this->patchJson(route('brand.v1.api-brand.update.put-patch.api', $brand->slug), [
            'name' => $this->faker->name,
            'name_en' => $this->faker->name,
            'slug' => $this->faker->slug,
        ])->assertForbidden();
    }

    /**
     * Permitted user can destroy a brand.
     *
     * @return void
     */
    public function testPermittedUserCanDeleteBrandDelete()
    {
        $this->actingAs(User::factory()->create())->assertAuthenticated();
        auth()->user()->assignRole(Roles::SUPER_ADMIN['name_en']);
        $this->_deleteAction();

        $this->actingAs(User::factory()->create())->assertAuthenticated();
        auth()->user()->givePermissionTo(Permissions::MANAGE_BRANDS);
        $this->_deleteAction();
    }

    /**
     * Guest or normal user can not destroy a brand.
     *
     * @return void
     */
    public function testGuestOrNormalUserCanNotDeleteBrandDelete()
    {
        $brand = Brand::factory()->create();
        $this->assertDatabaseHas(Brand::class, $brand->getAttributes())
            ->deleteJson(route('brand.v1.api-brand.destroy.delete.api', $brand->slug))
            ->assertUnauthorized();
        $this->assertDatabaseHas(Brand::class, $brand->getAttributes());

        $this->actingAs(User::factory()->create())->assertAuthenticated();
        $brand = Brand::factory()->create();
        $this->assertDatabaseHas(Brand::class, $brand->getAttributes())
            ->deleteJson(route('brand.v1.api-brand.destroy.delete.api', $brand->slug))
            ->assertForbidden();
        $this->assertDatabaseHas(Brand::class, $brand->getAttributes());
    }

    /**
     * Everyone can show a brand.
     *
     * @return void
     */
    public function testUserCanShowBrandGet()
    {
        $brand = Brand::factory()->create();
        $this->getJson(route('brand.v1.api-brand.show.get.api', $brand->slug))
            ->assertOk()
            ->assertJson([
                'status' => true,
                'has_error' => false,
            ]);
    }

    public function testUserCanNotShowBrandWithInvalidSlugGet()
    {
        $this->getJson(route('brand.v1.api-brand.show.get.api', "---"))
            ->assertNotFound();
    }

    /**
     * @param int $exists
     * @return void
     */
    private function _storeData(int $exists = 0): void
    {
        $this->assertDatabaseCount(Brand::class, $exists)
            ->postJson(route('brand.v1.api-brand.store.post.api'), [
                'name' => $this->faker->name,
                'name_en' => $this->faker->name,
                'slug' => $this->faker->slug,
                'image' => UploadedFile::fake()->image('brand.jpg'),
            ])->assertOk();
        $brand = Brand::query()->latest()->first();
        foreach ($brand->images()->get() as $media) {
            Storage::disk($media->disk)->assertExists($media->in_json);
        }
        $this->assertDatabaseCount(Brand::class, $exists + 1);
    }

    /**
     * @return void
     */
    private function _updateData(): void
    {
        $brand = Brand::factory()->create();
        $new_brand = [
            'name' => $this->faker->name,
            'name_en' => $this->faker->name,
            'slug' => $this->faker->slug,
        ];
        $this->assertDatabaseHas(Brand::class, $brand->getAttributes())
            ->assertDatabaseMissing(Brand::class, $new_brand)
            ->patchJson(route('brand.v1.api-brand.update.put-patch.api', $brand->slug), $new_brand)
            ->assertOk();
        $this->assertDatabaseMissing(Brand::class, $brand->getAttributes())
            ->assertDatabaseHas(Brand::class, $new_brand);
    }

    /**
     * @return void
     */
    private function _deleteAction(): void
    {
        $brand = Brand::factory()->create();
        $this->assertDatabaseHas(Brand::class, $brand->getAttributes())
            ->deleteJson(route('brand.v1.api-brand.destroy.delete.api', $brand->slug))
            ->assertOk();
        $this->assertDatabaseMissing(Brand::class, $brand->getAttributes());
    }
}
