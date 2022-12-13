<?php

namespace Modules\Rack\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Rack\Entities\RackRow;

class ProductExistsInRackRowRule implements Rule
{
    private mixed $rack_row;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(mixed $rack_row)
    {
        $this->rack_row = $rack_row;
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
        return RackRow::init()->productExistsInRackRow($this->rack_row, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('rack::validation.' . self::class);
    }
}
