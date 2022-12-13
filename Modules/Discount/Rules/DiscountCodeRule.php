<?php

namespace Modules\Discount\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Discount\Entities\Discount;

class DiscountCodeRule implements Rule
{
    /**
     * @var null
     */
    private $discount;

    /**
     * Create a new rule instance.
     *
     * @param null $discount
     */
    public function __construct($discount=null)
    {
        $this->discount = $discount;
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
        return Discount::init()->checkDoesntExistValidDiscount($value,$this->discount);
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
