<?php

namespace Modules\Tag\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Tag\Entities\Tag;

class TagRule implements Rule
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
        return Tag::init()->whereInIdsCount($value) == count($value) && method_exists($this->model,'tags');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('tag::validation.' . self::class);
    }
}
