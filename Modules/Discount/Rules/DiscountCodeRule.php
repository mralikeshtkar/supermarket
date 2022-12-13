<?php

namespace Modules\Discount\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Discount\Entities\Discount;

class DiscountCodeRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return Discount::init()->checkDoesntExistValidDiscount($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('discount::validation.'.self::class);
    }
}
