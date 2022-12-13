<?php

namespace Modules\Otp\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Modules\Core\Responses\Api\ApiResponse;
use Modules\Otp\Database\factories\OtpFactory;
use Modules\User\Entities\User;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Otp extends Model
{
    use HasFactory;

    #region Constants

    /**
     * Fill ables field.
     *
     * @var string[]
     */
    protected $fillable = [
        'mobile',
        'code',
    ];

    #endregion

    #region Methods

    /**
     * Initialize class.
     *
     * @return Otp
     */
    public static function init(): Otp
    {
        return new self();
    }

    /**
     * Model's factory.
     *
     * @return OtpFactory
     */
    protected static function newFactory(): OtpFactory
    {
        return OtpFactory::new();
    }

    /**
     * Generate an otp with specified mobile.
     *
     * @param string $mobile
     * @return Model|Builder
     */
    public function requestOtp(string $mobile): Model|Builder
    {
        $otp = $this->_checkCodeExpiration($mobile);
        //todo: send notification to user
        return $otp;
    }

    /**
     * Verify otp with code.
     *
     * @param string $mobile
     * @param string $code
     * @return JsonResponse
     */
    public function confirmOtp(string $mobile, string $code): JsonResponse
    {
        $otp = self::query()
            ->where(['mobile' => $mobile])
            ->first();
        if (!$otp || $this->_isExpired($otp))
            return ApiResponse::sendMessage(trans('otp::messages.the_request_is_not_valid'), Response::HTTP_BAD_REQUEST);
        if ($code != $otp->code)
            return ApiResponse::sendMessage(trans('otp::messages.the_verification_code_is_incorrect'), Response::HTTP_BAD_REQUEST);
        $user = User::query()->firstOrCreate(['mobile' => $otp->mobile], ['mobile' => $otp->mobile]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $otp->delete();
        return ApiResponse::message(trans('otp::messages.login_was_successful'))
            ->addData('token', $token)
            ->addData('mobile', $otp->mobile)
            ->send();
    }

    /**
     * Find an otp with specified mobile.
     *
     * @param string $mobile
     * @return Builder|Model|Otp
     */
    public function findOrFailByMobile(string $mobile): Model|Otp|Builder
    {
        return self::query()->where('mobile', $mobile)->firstOrFail();
    }

    /**
     * Generate a code and update otp.
     *
     * @return void
     */
    public function resendOtp()
    {
        $code = $this->_generateCode();
        $this->update([
            'code'=>$code
        ]);
    }

    /**
     * Generate a random code.
     *
     * @return int
     */
    private function _generateCode(): int
    {
        $digits = config('otp.verification_code_digits_length');
        return rand(
            pow(10, $digits - 1),
            pow(10, $digits) - 1
        );
    }

    /**
     * Check code expired after expiration verification code time ended.
     *
     * @param Model|Builder $otp
     * @return bool
     */
    private function _isExpired(Model|Builder $otp): bool
    {
        return $otp->updated_at
            ->addSeconds(config('otp.expiration_verification_code_time'))
            ->lt(now());
    }

    /**
     * If code isw expired generate a new code else return previous code.
     *
     * @param string $mobile
     * @return Model|Builder
     */
    private function _checkCodeExpiration(string $mobile): Model|Builder
    {
        $code = $this->_generateCode();
        info('Verification code is:' . $code);
        $otp = self::query()->firstOrCreate(['mobile' => $mobile], ['mobile' => $mobile, 'code' => $code]);
        if ($otp->exists() && $this->_isExpired($otp)) {
            $otp->update(['code' => $code]);
            return $otp->refresh();
        } else {
            return $otp;
        }
    }

    #endregion
}
