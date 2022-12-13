<?php

namespace Modules\Storeroom\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Product\Entities\Product;
use Modules\Storeroom\Entities\StoreroomEntrance;

class ProductExistsInEntranceRule implements Rule
{
    private mixed $entrance;

    /**
     * Create a new rule instance.
     *
     * @param mixed $entrance
     */
    public function __construct(mixed $entrance)
    {
        $this->entrance = $entrance;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return StoreroomEntrance::init()->productExistsInEntrance($this->entrance, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('storeroom::validation.' . self::class);
    }
}
