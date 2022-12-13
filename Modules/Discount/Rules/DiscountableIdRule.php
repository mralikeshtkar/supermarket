<?php

namespace Modules\Discount\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Category\Entities\Category;
use Modules\Category\Enums\CategoryStatus;
use Modules\Product\Entities\Product;
use Modules\Product\Enums\ProductStatus;

class DiscountableIdRule implements Rule
{
    private Request $request;
    private string $column;

    /**
     * Create a new rule instance.
     *
     * @param Request $request
     * @param string $column
     */
    public function __construct(Request $request,string $column = 'id')
    {
        $this->request = $request;
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
        $class = optional($this->request->discountables)->offsetGet('discountable_type');
        $ids = optional($this->request->discountables)->offsetGet('discountable_ids');
        return class_exists($class) && $class::init()
            ->whereIn($this->column, $ids)
            ->when($class == Product::class, function (Builder $builder) {
                $builder->where('status', ProductStatus::Accepted);
            })->when($class == Category::class, function (Builder $builder) {
                $builder->where('status', CategoryStatus::Accepted);
            })->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('discount::validation.' . self::class);
    }
}
