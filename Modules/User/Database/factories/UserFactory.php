<?php

namespace Modules\User\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\User\Entities\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'mobile' => $this->faker->numerify('+989#########'),
            'email' => $this->faker->unique()->email,
            'name' => $this->faker->name,
            'password' => bcrypt('password'),
        ];
    }
}

