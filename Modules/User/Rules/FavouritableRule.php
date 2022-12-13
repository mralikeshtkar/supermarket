<?php

namespace Modules\User\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;

class FavouritableRule implements Rule
{
    private string $column;

    /**
     *  Create a new rule instance.
     * @param string $column
     */
    public function __construct(string $column = 'id')
    {
        $this->column = $column;
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
        return class_exists($value['type']) &&
            is_subclass_of($value['type'], Model::class) &&
            method_exists($value['type'], 'favouriteUsers') &&
            $value['type']::where($this->column, $value['id'])->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('user::validation.'.self::class);
    }
}
