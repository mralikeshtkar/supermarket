<?php

namespace Modules\Address\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Address\Entities\City;
use Modules\User\Entities\User;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Address\Entities\Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $city = City::query()->with(['province'])->inRandomOrder()->first();
        return [
            'user_id' => User::query()->inRandomOrder()->first()->id,
            'province_id' => $city->province->id,
            'city_id' => $city->id,
            'name' => $this->faker->name,
            'mobile' => $this->faker->numerify('+989#########'),
            'address'=>$this->faker->sentence(10),
            'postal_code'=>$this->faker->numerify('##########'),
        ];
    }
}

