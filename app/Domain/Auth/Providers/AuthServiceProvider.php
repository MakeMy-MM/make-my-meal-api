<?php

namespace App\Domain\Auth\Providers;

use App\Domain\Auth\Services\LoginService;
use App\Domain\Auth\Services\LoginServiceInterface;
use App\Domain\Auth\Services\RegisterService;
use App\Domain\Auth\Services\RegisterServiceInterface;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public array $bindings = [
        RegisterServiceInterface::class => RegisterService::class,
        LoginServiceInterface::class => LoginService::class,
    ];
}
