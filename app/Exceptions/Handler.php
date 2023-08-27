<?php

namespace App\Exceptions;

use Throwable;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        if (request()->is('api/*')) {

            $this->renderable(function(TokenInvalidException $e, $request){
                return $this->generalApiResponse(401, [], "Invalid token", []);
            });

            $this->renderable(function (TokenExpiredException $e, $request) {
                return $this->generalApiResponse(401, [], "Token has Expired", []);
            });

            $this->renderable(function (JWTException $e, $request) {
                return $this->generalApiResponse(401, [], "Unauthorized", []);
            });

            $this->renderable(function (ValidationException $e, Request $request) {

                return $this->generalApiResponse(422, [], 'Invalid request', $e->errors());

            });

            $this->renderable(function (NotFoundHttpException $e, Request $request) {

                return $this->generalApiResponse(422, [], 'Record not found');

            });

        }

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
