<?php

namespace App\Http\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NotImplementedHttpException extends HttpException
{
    /**
     * Create a new HTTP exception.
     *
     * @param  array<string, string>  $headers
     */
    public function __construct(
        string $message = 'Not Implemented',
        ?\Throwable $previous = null,
        array $headers = [],
        int $code = 0,
    ) {
        parent::__construct(Response::HTTP_NOT_IMPLEMENTED, $message, $previous, $headers, $code);
    }
}
