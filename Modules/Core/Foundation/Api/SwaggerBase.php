<?php

namespace Modules\Core\Foundation\Api;

use OpenApi\Annotations as OA;

/**
 * @OA\Server(url="http://ario.jcoders.ir/api")
 * @OA\Server(url="http://127.0.0.1:8000/api/v1")
 * @OA\Info(
 *     version="1.0",
 *     title="Ario Web Services"
 * )
 */
class SwaggerBase
{
    public function index()
    {

    }
}
