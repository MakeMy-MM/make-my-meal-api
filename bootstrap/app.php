<?php

use App\Http\Exceptions\InternalServerErrorHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ModelNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        });

        $exceptions->render(function (\UnexpectedValueException|\LogicException|InternalServerErrorHttpException $e) {
            Log::emergency(
                sprintf('Internal Server Error: %s', $e->getMessage()),
                ['exception' => $e->getTraceAsString()],
            );

            throw new InternalServerErrorHttpException();
        });
    })->create();
