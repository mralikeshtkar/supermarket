<?php

namespace Modules\Otp\Tests\Feature\Controllers\V1\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Modules\Otp\Entities\Otp;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ApiAuthenticateControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * User can send request otp.
     *
     * @return void
     */
    public function testUserCanRequestOtpPostApi()
    {
        $mobile = $this->faker->numerify('+989#########');
        $this->assertDatabaseCount(Otp::class, 0)
            ->assertDatabaseMissing(Otp::class, ['mobile' => $mobile])
            ->post(route('otp.v1.api-authenticate.request-otp.post.api'), ['mobile' => $mobile])
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->where('status', true)
                ->where('code', Response::HTTP_OK)
                ->where('has_error', false)
                ->etc()
            );
        $this->assertDatabaseCount(Otp::class, 1)
            ->assertDatabaseHas(Otp::class, ['mobile' => $mobile]);
    }

    /**
     * User can not request otp with invalid mobile.
     *
     * @return void
     */
    public function testUserCannotRequestOtpWithInvalidMobilePostApi()
    {
        $mobile = $this->faker->numerify('#######');
        $this->assertDatabaseCount(Otp::class, 0)
            ->assertDatabaseMissing(Otp::class, ['mobile' => $mobile])
            ->post(route('otp.v1.api-authenticate.request-otp.post.api'), ['mobile' => $mobile])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['mobile'])
            ->assertJson(fn(AssertableJson $json) => $json->where('status', true)
                ->where('code', Response::HTTP_UNPROCESSABLE_ENTITY)
                ->where('has_error', true)
                ->etc()
            );
        $this->assertDatabaseCount(Otp::class, 0)
            ->assertDatabaseMissing(Otp::class, ['mobile' => $mobile]);
    }

    /**
     * User can confirm and login with valid data.
     *
     * @return void
     */
    public function testUserCanConfirmOtpPostApi()
    {
        $mobile = $this->faker->numerify('+989#########');
        $this->assertDatabaseCount(Otp::class, 0)
            ->assertDatabaseMissing(Otp::class, ['mobile' => $mobile])
            ->post(route('otp.v1.api-authenticate.request-otp.post.api'), ['mobile' => $mobile])
            ->assertOk();
        $this->assertDatabaseCount(Otp::class, 1)
            ->assertDatabaseHas(Otp::class, ['mobile' => $mobile]);
        $otp = Otp::query()->where(compact('mobile'))->first();
        $this->post(route('otp.v1.api-authenticate.confirm-otp.post.api'), $otp->only(['mobile', 'code']))
            ->assertOk()
            ->assertJson(fn(AssertableJson $json) => $json->where('status', true)
                ->where('has_error', false)
                ->has('data.token')
                ->has('data.mobile')
                ->etc()
            );
    }

    /**
     * User can't confirm and login with invalid data.
     *
     * @return void
     */
    public function testUserCanConfirmOtpWithInvalidDataPostApi()
    {
        $mobile = $this->faker->numerify('+989#########');
        $this->assertDatabaseCount(Otp::class, 0)
            ->assertDatabaseMissing(Otp::class, ['mobile' => $mobile])
            ->post(route('otp.v1.api-authenticate.request-otp.post.api'), ['mobile' => $mobile])
            ->assertOk();
        $this->assertDatabaseCount(Otp::class, 1)
            ->assertDatabaseHas(Otp::class, ['mobile' => $mobile]);
        $this->post(route('otp.v1.api-authenticate.confirm-otp.post.api'), [
            'mobile' => $mobile,
            'code' => str_repeat(0, config('otp.verification_code_digits_length')),
        ])->assertStatus(Response::HTTP_BAD_REQUEST);
    }
}
