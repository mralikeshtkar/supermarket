<?php

namespace Modules\Otp\Tests\Feature\Models;

use Modules\Otp\Entities\Otp;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OtpTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Check init static method return a object of model.
     *
     * @return void
     */
    public function testCheckInitializeAsModelObject()
    {
        $this->assertInstanceOf(Otp::class, Otp::init());
    }

    /**
     * Generate new otp with mobile.
     *
     * @return void
     */
    public function testGenerateNewOtpWithMobile()
    {
        $mobile = $this->faker->numerify('+989#########');
        $this->assertDatabaseCount(Otp::class, 0)
            ->assertDatabaseMissing(Otp::class, ['mobile' => $mobile]);
        Otp::init()->requestOtp($mobile);
        $this->assertDatabaseCount(Otp::class, 1)
            ->assertDatabaseHas(Otp::class, ['mobile' => $mobile]);
    }

    /**
     * Send previous code before expired time.
     *
     * @return void
     */
    public function testSendPreviousCodeBeforeExpiredTime()
    {
        $mobile = $this->faker->numerify('+989#########');
        $this->assertDatabaseMissing(Otp::class, ['mobile' => $mobile]);
        $otp = Otp::init()->requestOtp($mobile);
        $this->assertDatabaseHas(Otp::class, $otp->getAttributes())
            ->travel(config('otp.expiration_verification_code_time') - 10)->seconds(function () use ($otp) {
                $new_otp = Otp::init()->requestOtp($otp->mobile);
                $this->assertDatabaseHas(Otp::class,$otp->only(['mobile','code']))
                    ->assertDatabaseHas(Otp::class,$new_otp->only(['mobile','code']))
                    ->assertEquals($otp->code,$new_otp->code);
            });
    }

    /**
     * Send new code after expired time.
     *
     * @return void
     */
    public function testSendNewCodeAfterExpiredTime()
    {
        $mobile = $this->faker->numerify('+989#########');
        $this->assertDatabaseMissing(Otp::class, ['mobile' => $mobile]);
        $otp = Otp::init()->requestOtp($mobile);
        $this->assertDatabaseHas(Otp::class, $otp->getAttributes())
            ->travel(config('otp.expiration_verification_code_time') + 10)->seconds(function () use ($otp) {
                $new_otp = Otp::init()->requestOtp($otp->mobile);
                $this->assertDatabaseMissing(Otp::class,$otp->only(['mobile','code']))
                    ->assertDatabaseHas(Otp::class,$new_otp->only(['mobile','code']))
                    ->assertNotEquals($otp->code,$new_otp->code);
            });
    }
}
