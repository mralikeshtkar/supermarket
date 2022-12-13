<?php

namespace Modules\Brand\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Brand\Entities\Brand;

class BrandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Brand::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->company,
            'name_en' => $this->faker->unique()->company,
            'slug' => $this->faker->unique()->slug,
        ];
    }
}

