<?php

use App\Http\Exceptions\InternalServerErrorHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \App\Http\Middlewares\ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException|ModelNotFoundException $e) {
            $previous = $e->getPrevious();
            $modelException = $e instanceof ModelNotFoundException
                ? $e
                : ($previous instanceof ModelNotFoundException ? $previous : null);

            if ($modelException === null) {
                return response()->json(['message' => 'Not found'], Response::HTTP_NOT_FOUND);
            }

            $entity = class_basename($modelException->getModel());

            return response()->json(['message' => "{$entity} not found"], Response::HTTP_NOT_FOUND);
        });

        $exceptions->render(function (\UnexpectedValueException|\LogicException|InternalServerErrorHttpException $e) {
            $previous = $e->getPrevious();

            Log::emergency(
                sprintf('Internal Server Error: %s', $previous?->getMessage() ?? $e->getMessage()),
                ['exception' => $previous?->getTraceAsString() ?? $e->getTraceAsString()],
            );

            return response()->json(['message' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();
