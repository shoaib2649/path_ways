<?php
use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (Application $app) {
            // grab the router from the container
            $router = $app->make(Router::class);

            // register your API routes under the "api" prefix
            $router->group([
                'prefix'     => 'api',
                'middleware' => 'api',
                'namespace'  => 'App\\Http\\Controllers',
            ], function () {
                require base_path('routes/api.php');
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
