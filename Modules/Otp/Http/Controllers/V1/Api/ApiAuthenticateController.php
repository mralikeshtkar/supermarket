<?php

namespace Modules\Otp\Http\Controllers\V1\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Core\Rules\MobileRule;
use Modules\Otp\Entities\Otp;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiAuthenticateController
{
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="درخواست کد فعالسازی",
     *     description="درخواست کد فعالسازی",
     *     tags={"حساب کاربری"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"mobile"},
     *                 @OA\Property(
     *                     property="mobile",
     *                     type="string",
     *                     description="شماره موبایل"
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
    public function requestOtp(Request $request)
    {
        ApiResponse::init($request->all(), [
            'mobile' => ['bail', 'required', new MobileRule()],
        ])->validate();
        try {
            $otp = Otp::init()->requestOtp(to_valid_mobile_number($request->get('mobile')));
            return ApiResponse::message(trans('otp::messages.activation_code_sent_successfully'))
                ->addData('code', $otp->code)
                ->send();
        } catch (Throwable $e) {
            return ApiResponse::sendMessage($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/confirm",
     *     summary="اعتبار سنجی کد فعالسازی",
     *     description="اعتبار سنجی کد فعالسازی",
     *     tags={"حساب کاربری"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"mobile","code"},
     *                 @OA\Property(
     *                     property="mobile",
     *                     type="string",
     *                     description="شماره موبایل"
     *                 ),
     *                 @OA\Property(
     *                     property="code",
     *                     type="number",
     *                     description="کد فعالسازی"
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
    public function confirmOtp(Request $request)
    {
        ApiResponse::init($request->all(), [
            'mobile' => ['bail', 'required', new MobileRule()],
            'code' => ['bail', 'required', 'numeric', 'digits:' . config('otp.verification_code_digits_length')],
        ])->validate();
        try {
            return Otp::init()->confirmOtp(to_valid_mobile_number($request->get('mobile')), $request->get('code'));
        } catch (Throwable $e) {
            return ApiResponse::sendMessage(trans('otp::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generate new activation code and send to user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resendOtp(Request $request)
    {
        ApiResponse::init($request->all(), [
            'mobile' => ['bail', 'required', new MobileRule()],
        ], [], trans('otp::validation.attributes'))->validate();
        try {
            $otp = Otp::init()->findOrFailByMobile($request->mobile);
            $otp->resendOtp();
            return ApiResponse::sendMessage(trans('otp::messages.otp_resend_sent'));
        } catch (ModelNotFoundException $e) {
            return ApiResponse::sendError(trans('otp::messages.otp_not_found'), Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return ApiResponse::message(trans('otp::messages.internal_error'), Response::HTTP_INTERNAL_SERVER_ERROR)
                ->addError('message', $e->getMessage())
                ->send();
        }
    }
}
