<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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
        $exceptions->render(function (ModelNotFoundException $e) {
            return response()->json(
                ['message' => class_basename($e->getModel()) . ' not found'],
                Response::HTTP_NOT_FOUND,
            );
        });

        $exceptions->render(function (NotFoundHttpException $e) {
            $previous = $e->getPrevious();
            $message = $previous instanceof ModelNotFoundException
                ? class_basename($previous->getModel()) . ' not found'
                : 'Not found';

            return response()->json(
                ['message' => $message],
                Response::HTTP_NOT_FOUND,
            );
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json(
                ['message' => 'Unauthorized'],
                Response::HTTP_UNAUTHORIZED,
            );
        });

        $exceptions->render(function (UniqueConstraintViolationException $e) {
            return response()->json(
                ['message' => 'Conflict'],
                Response::HTTP_CONFLICT,
            );
        });

        $exceptions->render(function (HttpExceptionInterface $e) {
            $statusCode = $e->getStatusCode();
            $message = $e->getMessage() ?: (Response::$statusTexts[$statusCode] ?? 'Error');

            return response()->json(
                ['message' => $message],
                $statusCode,
            );
        });

        $exceptions->respond(function (Response $response, \Throwable $e) {
            if (
                $e instanceof HttpExceptionInterface
                || $e instanceof AuthenticationException
                || $e instanceof ModelNotFoundException
                || $e instanceof UniqueConstraintViolationException
            ) {
                return $response;
            }

            Log::emergency(
                sprintf('Internal Server Error: %s', $e->getMessage()),
                ['exception' => $e->getTraceAsString()],
            );

            return response()->json(
                ['message' => 'Internal Server Error'],
                Response::HTTP_INTERNAL_SERVER_ERROR,
            );
        });
    })->create();
