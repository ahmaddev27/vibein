<?php


namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => $exception->getMessage(),
                'data' => null,
            ], 422);
        }

        return parent::render($request, $exception);
    }
}
