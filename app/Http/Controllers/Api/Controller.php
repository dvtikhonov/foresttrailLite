<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Foresttrail",
 *      description="Все наши сервисы работают через данное API",
 *      @OA\Contact(
 *          email="tikhonov.kv@ya.ru"
 *      ),
 * )
 * @OA\Server(
 *      url="http://foresttrail.loc/api/v1",
 *      description="General server"
 * )
 * @OA\Server(
 *      url="http://foresttrail.ru/api/v1",
 *      description="General server"
 * )
 *
 *   @OA\Components(
 * )
 *
 *
 *
 *
 *

 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}