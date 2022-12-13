<?php

namespace Modules\Core\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneNumberRule implements Rule
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
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return preg_match('/^(?:0|98|\+98|\+980|0098|098|00980)?(9\d{9})$/', $value) || preg_match('/^0[0-9]{2,}[0-9]{7,}$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('core::validation.' . self::class);
    }
}
