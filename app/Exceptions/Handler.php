<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = ['password', 'password_confirmation'];

    public function register(): void
    {
        $this->renderable(function (\DomainException $e) {
            return response()->json([
                'error' => 'domain_error',
                'message' => $e->getMessage(),
            ], 422);
        });

        $this->renderable(function (ValidationException $e) {
            return response()->json([
                'error' => 'validation_error',
                'message' => 'The given data was invalid.',
                'errors' => $e->errors(),
            ], 422);
        });

        $this->renderable(function (AuthenticationException $e) {
            return response()->json([
                'error' => 'unauthenticated',
                'message' => 'Token is invalid or expired.',
            ], 401);
        });

        $this->renderable(function (HttpException $e) {
            return response()->json([
                'error' => 'http_error',
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        });

        $this->renderable(function (Throwable $e) {
            if (app()->environment('production')) {
                return response()->json([
                    'error' => 'server_error',
                    'message' => 'An unexpected error occurred.',
                ], 500);
            }
        });
    }
}
