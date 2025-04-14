<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                // Custom JSON response for API
                return response()->json([
                    'status'=>false,
                    'code'=>401,
                    'message' => 'Unauthorized,please login.',
                    'data'=>[],
                ],401);
            }
//            // Redirect for web requests
//            return redirect()->guest(route('login'))->with('error', 'Please log in.');
        });

    })->create();

