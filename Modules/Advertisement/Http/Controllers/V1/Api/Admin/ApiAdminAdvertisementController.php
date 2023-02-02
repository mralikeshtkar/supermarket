<?php

namespace Modules\Advertisement\Http\Controllers\V1\Api\Admin;

use App\Http\Controllers\Controller;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Advertisement\Entities\Advertisement;
use Modules\Advertisement\Enums\AdvertisementPlace;
use Modules\Advertisement\Enums\AdvertisementStatus;
use Modules\Advertisement\Transformers\Api\Admin\ApiAdminAdvertisementResource;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAdminAdvertisementController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $advertisements = Advertisement::init()->selectColumns(['id', 'place', 'status', 'created_at'])
            ->withRelationships(['image'])
            ->paginateAdmin($request);
        $resource = ApiPaginationResource::make($advertisements)->additional(['itemsResource' => ApiAdminAdvertisementResource::class]);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('advertisements', $resource)
            ->addData('places', collect(AdvertisementPlace::asArray())->map(function ($item) {
                return [
                    'title' => AdvertisementPlace::getDescription($item),
                    'value' => $item,
                ];
            })->values())
            ->addData('statuses', collect(AdvertisementStatus::asArray())->map(function ($item) {
                return [
                    'title' => AdvertisementStatus::getDescription($item),
                    'value' => $item,
                ];
            })->values())->send();
    }

    /**
     * @param Request $request
     * @param $advertisement
     * @return JsonResponse
     */
    public function show(Request $request, $advertisement)
    {
        $advertisement = Advertisement::init()->selectColumns(['id', 'place', 'status', 'created_at'])
            ->withRelationships(['image'])
            ->findOrFailById($advertisement);
        return ApiResponse::message(trans("The operation was done successfully"))
            ->addData('advertisement', new ApiAdminAdvertisementResource($advertisement))
            ->send();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        ApiResponse::init($request->all(), [
            'image' => ['required', 'image'],
            'place' => ['required', new EnumValue(AdvertisementPlace::class)],
            'status' => ['required', new EnumValue(AdvertisementStatus::class)],
        ])->validate();
        try {
            return DB::transaction(function () use ($request) {
                Advertisement::init()->store($request);
                return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
            });
        } catch (Throwable $e) {
            return ApiResponse::sendError(trans("Internal server error"));
        }
    }

    /**
     * @param Request $request
     * @param $advertisement
     * @return JsonResponse|mixed
     */
    public function update(Request $request, $advertisement)
    {
        /** @var Advertisement $advertisement */
        $advertisement = Advertisement::init()->withRelationships(['image'])->findOrFailById($advertisement);
        ApiResponse::init($request->all(), [
            'image' => ['nullable', 'image'],
            'place' => ['required', new EnumValue(AdvertisementPlace::class)],
            'status' => ['required', new EnumValue(AdvertisementStatus::class)],
        ])->validate();
        try {
            return DB::transaction(function () use ($request, $advertisement) {
                $advertisement->updateRow($request);
                return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
            });
        } catch (Throwable $e) {
            return ApiResponse::sendError(trans("Internal server error"));
        }
    }

    /**
     * @param Request $request
     * @param $advertisement
     * @return JsonResponse|mixed
     */
    public function destroy(Request $request, $advertisement)
    {
        /** @var Advertisement $advertisement */
        $advertisement = Advertisement::init()->withRelationships(['image'])->findOrFailById($advertisement);
        try {
            return DB::transaction(function () use ($request, $advertisement) {
                $advertisement->destroyRow($request);
                return ApiResponse::message(trans("The operation was done successfully"), Response::HTTP_CREATED)->send();
            });
        } catch (Throwable $e) {
            return ApiResponse::sendError(trans("Internal server error"));
        }
    }
}
