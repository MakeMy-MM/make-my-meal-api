<?php

namespace App\Http\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class InternalServerErrorHttpException extends HttpException
{
    /**
     * Create a new HTTP exception.
     *
     * @param  array<string, string>  $headers
     */
    public function __construct(
        string $message = 'Internal Server Error',
        ?\Throwable $previous = null,
        array $headers = [],
        int $code = 0,
    ) {
        parent::__construct(Response::HTTP_INTERNAL_SERVER_ERROR, $message, $previous, $headers, $code);
    }
}
