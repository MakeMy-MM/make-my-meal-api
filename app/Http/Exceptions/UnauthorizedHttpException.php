<?php

namespace App\Http\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedHttpException extends HttpException
{
    /**
     * Create a new HTTP exception.
     *
     * @param  array<string, string>  $headers
     */
    public function __construct(
        string $message = 'Unauthorized',
        ?\Throwable $previous = null,
        int $code = 0,
        array $headers = [],
    ) {
        parent::__construct(Response::HTTP_UNAUTHORIZED, $message, $previous, $headers, $code);
    }
}
