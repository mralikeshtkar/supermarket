<?php

namespace Modules\LogActivity\Entities;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogActivity extends Activity
{

    #region Methods

    /**
     * @return LogActivity
     */
    public static function init(): LogActivity
    {
        return new self();
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAdminIndexPaginate(Request $request): LengthAwarePaginator
    {
        return self::query()
            ->with(['causer:id,name,mobile'])
            ->latest()
            ->paginate();
    }

    #endregion

}
