<?php

namespace Modules\Discount\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Modules\Core\Responses\Api\ApiResponse;

class DiscountIsInvalidException extends Exception
{
    public function render(Request $request)
    {
        if ($request->wantsJson())
            return ApiResponse::sendError($this->getMessage(), $this->getCode());
    }
}
