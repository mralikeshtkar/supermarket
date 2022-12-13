<?php

namespace Modules\Core\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class ProductModelRule implements Rule
{
    private array $extensions;

    /**
     * Create a new rule instance.
     *
     * @param array $extensions
     */
    public function __construct(array $extensions)
    {
        $this->extensions = $extensions;
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
        return $value instanceof UploadedFile && in_array(strtolower($value->getClientOriginalExtension()), $this->extensions);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('core::validation.' . self::class);
    }
}
