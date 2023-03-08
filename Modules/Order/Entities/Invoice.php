<?php

namespace Modules\Order\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Enums\OrderInvoiceStatus;

class Invoice extends Model
{
    use HasFactory;

    #region Constance

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'transactionId',
        'gateway',
        'amount',
        'is_pay_in_person',
        'status',
    ];

    protected $casts = [
        'is_pay_in_person' => 'bool',
    ];

    #endregion

    #region Methods

    /**
     * @return Invoice
     */
    public static function init(): Invoice
    {
        return new self();
    }

    /**
     * @param $period
     * @return \Illuminate\Support\Collection
     */
    public function getTotalSaleInPeriod($period): \Illuminate\Support\Collection
    {
        return self::query()
            ->selectRaw('SUM((amount)::integer) AS amount_sum,created_at')
            ->groupBy('created_at')
            ->whereBetween('created_at', [today()->subDays($period), today()])
            ->get();
    }

    /**
     * Get translated status.
     *
     * @return string
     */
    public function getTranslatedStatus(): string
    {
        return OrderInvoiceStatus::getDescription(intval($this->status));
    }

    /**
     * Get css class status.
     *
     * @return string
     */
    public function getCssClassStatus(): string
    {
        return OrderInvoiceStatus::coerce(intval($this->status))->getCssClass();
    }

    #endregion

    #region Scopes

    /**
     * @param Builder $builder
     * @return void
     */
    public function scopeSuccess(Builder $builder)
    {
        $builder->where('status', OrderInvoiceStatus::Success);
    }

    #endregion
}
