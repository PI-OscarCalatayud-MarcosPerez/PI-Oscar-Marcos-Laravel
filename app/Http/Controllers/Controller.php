<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="MOKeys API",
 *      description="API documentation for MOKeys E-commerce",
 *      @OA\Contact(
 *          email="admin@mokeys.com"
 *      )
 * )
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Demo API Server"
 * )
 */
abstract class Controller
{
    //
}
