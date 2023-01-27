<?php

namespace Modules\Vote\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VoteItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \Modules\Vote\Entities\VoteItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }
}

