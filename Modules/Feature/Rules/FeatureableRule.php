<?php

namespace Modules\Feature\Rules;

use Illuminate\Contracts\Validation\Rule;

class FeatureableRule implements Rule
{
    private string $column;

    /**
     * Create a new rule instance.
     *
     * @return void
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
            method_exists($value['type'], 'features') &&
            $value['type']::where($this->column, $value['id'])->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('feature::validation.' . self::class);
    }
}
