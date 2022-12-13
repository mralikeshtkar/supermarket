<?php

namespace Modules\Category\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Category\Entities\Category;

class CategoryRule implements Rule
{
    private string $model;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $model)
    {
        $this->model = $model;
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
        return Category::init()->whereExistsAcceptedWithIds($value) && method_exists($this->model,'categories');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('category::validation.' . self::class);
    }
}
