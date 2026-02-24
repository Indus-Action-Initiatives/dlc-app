<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
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
        $this->reportable(function (Throwable $e) {
            //
        });

       $this->renderable(function (Throwable $e, $request) {

        if ($request->expectsJson() || $request->is('api/*')) {

            // Default values
            $statusCode = 500;
            $message = $e->getMessage();

            // Map exceptions to HTTP codes
            switch (true) {

                case $e instanceof \Illuminate\Validation\ValidationException:
                    $statusCode = 422;
                    $message = 'Validation failed';
                    $errors = $e->errors();
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'errors' => $errors
                    ], $statusCode);

                case $e instanceof \Illuminate\Auth\AuthenticationException:
                case $e instanceof \Tymon\JWTAuth\Exceptions\JWTException:
                    $statusCode = 401;
                    $message = 'Unauthorized access';
                    break;

                case $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException:
                    $statusCode = 404;
                    $message = 'Resource not found';
                    break;

                case $e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException:
                    $statusCode = 405;
                    $message = 'HTTP method not allowed';
                    break;
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $statusCode);
        }

        // For web requests, fallback to Laravel default
        return null;
    });


    }
}
