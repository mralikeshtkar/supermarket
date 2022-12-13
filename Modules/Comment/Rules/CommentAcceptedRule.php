<?php

namespace Modules\Comment\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Comment\Entities\Comment;

class CommentAcceptedRule implements Rule
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
        return Comment::init()->existAcceptedComment($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('comment::validation.' . self::class);
    }
}
