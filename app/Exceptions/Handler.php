<?php

namespace App\Exceptions;

use Throwable;
use App\Traits\ApiResponser;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
        $this->renderable(function(TokenInvalidException $e, $request){
            return $this->generalApiResponse(401, [], "Invalid token", []);
        });

        $this->renderable(function (TokenExpiredException $e, $request) {
            return $this->generalApiResponse(401, [], "Token has Expired", []);
        });

        $this->renderable(function (JWTException $e, $request) {
            return $this->generalApiResponse(401, [], "Unauthorized", []);
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
