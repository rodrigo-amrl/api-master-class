<?php

use App\Traits\ApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

$classTrait = new class {
    use ApiResponses;
};


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            Route::middleware('api')->prefix('api')
                ->group(base_path('routes/api.php'));
            Route::middleware('api')->prefix('api/v1')
                ->group(base_path('routes/api_v1.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) use ($classTrait) {

        $exceptions->render(function (Throwable $e, Request $request) use ($classTrait) {
            $className =  get_class($e);

            $index = strrpos($className, '\\');
            return  $classTrait->error([
                'type' => substr($className, $index + 1),
                'status' => 0,
                'message' => $e->getMessage()
            ]);
        });
    })->create();
