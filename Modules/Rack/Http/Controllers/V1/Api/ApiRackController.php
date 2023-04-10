<?php

namespace Modules\Rack\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\Enum;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Rack\Entities\Rack;
use Modules\Rack\Entities\RackRow;
use Modules\Rack\Enums\RackStatus;
use Modules\Rack\Transformers\RackResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiRackController extends Controller
{

    public function products(Request $request)
    {
        foreach (Rack::all() as $item) {
            $rows = $item->rows()->orderByPriorityAsc()->get()->pluck('id')->toArray();
            dump($rows);
        }
        dd("ok");
        return ApiResponse::message(trans('rack::messages.received_information_successfully'))
            ->addData('racks',RackResource::collection(Rack::init()->allRackRowsWithProducts()))
            ->send();
    }
}
