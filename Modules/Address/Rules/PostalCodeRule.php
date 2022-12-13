<?php

namespace Modules\Address\Rules;

use Illuminate\Contracts\Validation\Rule;

class PostalCodeRule implements Rule
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
        return preg_match('/(?!(\d)\1{3})[13-9]{4}[1346-9][013-9]{5}/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('address::validation.'.self::class);
    }
}
