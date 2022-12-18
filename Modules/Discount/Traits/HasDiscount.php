<?php

namespace Modules\Discount\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Discount\Entities\Discount;

trait HasDiscount
{
    private string $DISCOUNT_KEY = 'global_discount_id';

    private string $DISCOUNT_RELATION_NAME = 'globalDiscount';

    use RefreshDatabase;

    /**
     * Initialize HasDiscount trait.
     *
     * @return void
     */
    public function initializeHasDiscount()
    {
        static::addGlobalScope(function ($query) {
            $query->globalDiscount();
        });
        $this->append(['is_percent_discount','discount_amount','discount_price','final_price']);
    }

    /**
     * @return null
     */
    public function getIsPercentDiscountAttribute()
    {
        return $this->isPercentDiscount();
    }

    /**
     * @return null
     */
    public function isPercentDiscount()
    {
        return $this->_hasGlobalDiscount() ? $this->getRelation($this->DISCOUNT_RELATION_NAME)->is_percent : null;
    }

    public function getDiscountAmountAttribute(): int
    {
        return intval($this->getDiscountAmount());
    }

    public function getDiscountAmount()
    {
        return $this->_hasGlobalDiscount() ? $this->getRelation($this->DISCOUNT_RELATION_NAME)->amount : null;
    }

    /**
     * @return float|int|null
     */
    public function getDiscountPriceAttribute(): float|int|null
    {
        return $this->getDiscountPrice();
    }

    /**
     * @return float|int|null
     */
    public function getDiscountPrice(): float|int|null
    {
        $discount_amount = $this->getDiscountAmount();
        return $this->_hasGlobalDiscount() ? (
        $this->isPercentDiscount() ?
            ($this->price / 100) * $discount_amount :
            $discount_amount
        ) : 0;
    }

    public function getFinalPriceAttribute()
    {
        return $this->getFinalPrice();
    }

    public function getFinalPrice()
    {
        $discount_price = $this->getDiscountPrice();
        return $discount_price ?
            ($discount_price > $this->price ? 0 : $this->price - $discount_price) :
            $this->price;
    }

    public function getGlobalDiscount()
    {
        return $this->_hasGlobalDiscount() ? $this->getRelation($this->DISCOUNT_RELATION_NAME) : null;
    }

    public function scopeGlobalDiscount(Builder $builder)
    {
        $builder->addSelect([
            $this->DISCOUNT_KEY => Discount::query()
                ->selectRaw("discounts.id")
                ->onlyGlobalDiscount()
                ->hasDiscount($this->getMorphClass())
                ->usageLimitationGreaterThanZeroOrNull()
                ->codeIsStartedOrNull()
                ->codeDoesntExpireOrNull()
                ->accepted()
                ->orderByAmountDesc()
                ->orderByIsPercentDesc()
                ->limit(1),
        ])->with('globalDiscount:id,amount,is_percent');
    }

    /**
     * @return bool
     */
    private function _hasGlobalDiscount(): bool
    {
        return $this->relationLoaded($this->DISCOUNT_RELATION_NAME) && $this[$this->DISCOUNT_RELATION_NAME];
    }

    /**
     * @return BelongsTo
     */
    public function globalDiscount(): BelongsTo
    {
        return $this->belongsTo(Discount::class, $this->DISCOUNT_KEY);
    }
}
