<?php

namespace Modules\Category\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Category\Enums\CategoryStatus;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Category\Entities\Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=>$this->faker->name,
            'slug'=>$this->faker->slug,
            'status'=>CategoryStatus::Accepted,
        ];
    }
}

