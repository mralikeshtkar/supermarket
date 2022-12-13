<?php

namespace Modules\User\Http\Controllers\V1\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Transformers\Api\ApiPaginationResource;
use Modules\User\Rules\FavouritableRule;
use Modules\User\Transformers\V1\Api\ApiUserOrderResource;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiUserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/users/{id}",
     *     summary="بروزرسانی اطلاعات",
     *     description="",
     *     tags={"کاربر"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"_method","name"},
     *                 @OA\Property(
     *                     property="_method",
     *                     type="string",
     *                     default="put",
     *                     enum={"put"},
     *                     description="این مقدار باید بصورت ثابت شود",
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="نام"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="ایمیل"
     *                 ),
     *                 @OA\Property(
     *                     property="old_password",
     *                     type="string",
     *                     description="کلمه عبور قبلی"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="کلمه عبور جدید"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="عملیات موفق",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function update(Request $request)
    {
        ApiResponse::init($request->all(), [
            'name' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'old_password' => ['required_with:password', 'string'],
            'password' => ['required_with:old_password', 'string'],
        ])->validate();
        if (!$request->user()->checkPassword($request->old_password))
            return ApiResponse::sendError(trans("user::messages.the_password_is_wrong"), Response::HTTP_BAD_REQUEST);
        $request->user()->update(collect([
            'name' => $request->name,
            'email' => $request->email,
        ])->when($request->filled('password'), function (Collection $collection) use ($request) {
            $collection->put('password', Hash::make($request->password));
        }));
        return ApiResponse::sendMessage(trans("Registration information completed successfully"));
    }

    /**
     * Add to favourites from call api.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function like(Request $request)
    {
        $request->merge([
            'favouritable' => [
                'id' => Arr::get($request->get('favouritable'), 'id'),
                'type' => Relation::getMorphedModel(Arr::get($request->get('favouritable'), 'type')),
            ],
        ]);
        ApiResponse::init($request->all(), [
            'favouritable' => ['required', 'array:id,type', new FavouritableRule()],
        ], [], trans('user::validation.attributes'))->validate();
        try {
            $request->user()->like($request);
            return ApiResponse::message(trans('user::messages.add_to_favorites_successfully'))->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::message(trans('user::messages.favouritable_not_found'), Response::HTTP_NOT_FOUND)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('user::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * Remove from favourites from call api.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function dislike(Request $request)
    {
        $request->merge([
            'favouritable' => [
                'id' => Arr::get($request->get('favouritable'), 'id'),
                'type' => Relation::getMorphedModel(Arr::get($request->get('favouritable'), 'type')),
            ],
        ]);
        ApiResponse::init($request->all(), [
            'favouritable' => ['required', 'array:id,type', new FavouritableRule()],
        ], [], trans('user::validation.attributes'))->validate();
        try {
            $request->user()->dislike($request);
            return ApiResponse::message(trans('user::messages.remove_from_favorites_successfully'))->send();
        } catch (ModelNotFoundException $e) {
            return ApiResponse::message(trans('user::messages.favouritable_not_found'), Response::HTTP_NOT_FOUND)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::message(trans('user::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->hasError()
                ->addError('message', $e->getMessage())
                ->send();
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function favourites(Request $request)
    {
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('favourites', $request->user()->getFavourites($request))
            ->send();
    }

    /**
     * @OA\Get(
     *     path="/user/orders",
     *     summary="لیست سفارشات کاربر",
     *     description="لیست سفارشات کاربر",
     *     tags={"کاربر"},
     *     @OA\Response(
     *         response=200,
     *         description="عملیات موفق",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function orders(Request $request)
    {
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('orders', ApiPaginationResource::make($request->user()->getOrders())->additional(['itemsResource' => ApiUserOrderResource::class]))
            ->send();
    }

    /**
     * @OA\Get(
     *     path="/user/orders/{id}",
     *     summary="نمایش جزئیات سفارش کاربر",
     *     description="نمایش جزئیات سفارش کاربر",
     *     tags={"کاربر"},
     *     @OA\Parameter(
     *         description="شناسه سفارش",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="number"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="عملیات موفق",
     *         @OA\JsonContent()
     *     ),
     * )
     */
    public function showOrder(Request $request, $order)
    {
        $order = $request->user()->findOrFailOrderById($order);
        return ApiResponse::message(trans('user::messages.received_information_successfully'))
            ->addData('order', ApiUserOrderResource::make($order))
            ->send();
    }
}
