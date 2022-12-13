<?php

namespace Modules\Media\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Media\Entities\Media;
use Modules\Product\Entities\Product;

class MediaModelRule implements Rule
{
    private Request $request;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
        return class_exists($value['type']) &&
            method_exists($value['type'], 'scopeFindByIdWithCollection') &&
            $value['type']::findByIdWithCollection($value,$this->request)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('media::validation.'.self::class);
    }
}
