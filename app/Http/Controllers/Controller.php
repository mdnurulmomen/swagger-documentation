<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="PetShop API Swagger-Documentation",
 *      description="PetShop API description",
 *      @OA\Contact(
 *          email="mdnurulmomen.bd@gmail.com"
 *      )
 * ),
 * @OA\Server(
 *     url="http://127.0.0.1:8000/",
 * )
 * @OA\SecurityScheme(
 *    securityScheme="bearerAuth",
 *    type="http",
 *    scheme="bearer",
 *    description="Authorization token obtained from logging in",
 *    name="Authorization",
 *    in="header",
 *    bearerFormat="JWT",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
