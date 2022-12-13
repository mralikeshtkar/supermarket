<?php

namespace Modules\Permission\Entities;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class Permission extends \Spatie\Permission\Models\Permission
{
    #region Constance

    protected $appends = [
        'translated_name'
    ];

    #endregion

    #region Mutators

    /**
     * @return array|string|Translator|Application|null
     */
    public function getTranslatedNameAttribute(): array|string|Translator|Application|null
    {
        return trans('permission::permissions.' . $this->name);
    }

    #endregion

    #region Methods

    /**
     * Initialize class.
     *
     * @return Permission
     */
    public static function init(): Permission
    {
        return new self();
    }

    public function getAll()
    {
        return self::query()->get();
    }

    #endregion
}
