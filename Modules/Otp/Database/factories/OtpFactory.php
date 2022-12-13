<?php

namespace Modules\Otp\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Otp\Entities\Otp;

class OtpFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Otp::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'mobile' => $this->faker->numerify('+989#########'),
            'code'=>$this->faker->randomNumber(6,true),
        ];
    }
}

