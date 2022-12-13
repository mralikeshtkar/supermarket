<?php

namespace Modules\Discount\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Category\Entities\Category;
use Modules\Category\Enums\CategoryStatus;
use Modules\Product\Entities\Product;
use Modules\Product\Enums\ProductStatus;

class DiscountableTypeRule implements Rule
{


    /**
     * Create a new rule instance.
     */
    public function __construct()
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return class_exists($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('discount::validation.' . self::class);
    }
}
