<?php

namespace Modules\Discount\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Discount\Entities\Discount;

trait HasDiscount
{
    private string $DISCOUNT_KEY = 'global_discount';

    use RefreshDatabase;

    /**
     * Initialize HasDiscount trait.
     *
     * @return void
     */
    public function initializeHasDiscount()
    {
        //array_push($this->appends,"discountAmount","finalPrice");
    }

    public function getGlobalDiscountAmountAttribute()
    {
        return $this->isGlobalDiscountPercent();
    }

    public function getFinalPriceAttribute()
    {
        return $this->getAttribute($this->DISCOUNT_KEY) ?
            (
            $this->isGlobalDiscountPercent() ?
                (($this->getAttribute('price') / 100) * $this->getAttribute($this->DISCOUNT_KEY)['amount']) :
                ($this->getAttribute('price') - $this->getAttribute($this->DISCOUNT_KEY)['amount'])
            ) :
            $this->getAttribute('price');
    }

    public function getDiscountAmountAttribute()
    {
        return $this->getAttribute('price') - $this->getFinalPriceAttribute();
    }

    public function isGlobalDiscountPercent()
    {
        return $this->getAttribute($this->DISCOUNT_KEY) ? $this->getAttribute($this->DISCOUNT_KEY)['is_percent'] : null;
    }

    public function scopeGlobalDiscount(Builder $builder)
    {
        $builder->addSelect([
            $this->DISCOUNT_KEY => Discount::query()
                ->selectRaw("json_strip_nulls(json_build_object('amount', discounts.amount,'is_percent', discounts.is_percent))")
                ->onlyGlobalDiscount()
                ->hasDiscount($this->getMorphClass())
                ->usageLimitationGreaterThanZeroOrNull()
                ->codeIsStartedOrNull()
                ->codeDoesntExpireOrNull()
                ->orderPriority()
                ->limit(1),
        ]);
    }
}
