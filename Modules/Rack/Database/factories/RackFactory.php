<?php

namespace Modules\Rack\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RackFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Rack\Entities\Rack::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $number = $this->faker->unique()->numberBetween();
        return [
            'title' => "قفسه " . $number,
            'priority' => $number,
        ];
    }
}

