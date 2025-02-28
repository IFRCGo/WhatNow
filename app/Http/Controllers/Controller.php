<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\OpenApi(
 *    @OA\Info(
 *        title="Whatnow",
 *        description="Whatnow front end API documentation",
 *        version="1.0.0",
 *    ),
 *    @OA\Server(
 *        url=L5_SWAGGER_CONST_HOST,
 *        description="API Base URL"
 *    )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
