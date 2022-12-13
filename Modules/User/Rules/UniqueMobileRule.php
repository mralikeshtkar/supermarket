<?php

namespace Modules\User\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\User\Entities\User;

class UniqueMobileRule implements Rule
{
    private mixed $user;

    /**
     * Create a new rule instance.
     *
     * @param mixed $user
     */
    public function __construct(mixed $user = null)
    {
        $this->user = $user;
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
        return User::init()->mobileIsUnique(to_valid_mobile_number($value), $this->user);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('user::validation.' . self::class);
    }
}
