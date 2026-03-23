<?php

namespace App\Domain\Auth\Providers;

use App\Domain\Auth\Services\LoginService;
use App\Domain\Auth\Services\LoginServiceInterface;
use App\Domain\Auth\Services\RegisterService;
use App\Domain\Auth\Services\RegisterServiceInterface;
use App\Domain\Auth\Services\TokenService;
use App\Domain\Auth\Services\TokenServiceInterface;
use Carbon\CarbonInterval;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public array $bindings = [
        RegisterServiceInterface::class => RegisterService::class,
        LoginServiceInterface::class => LoginService::class,
        TokenServiceInterface::class => TokenService::class,
    ];

    public function boot(): void
    {
        Passport::personalAccessTokensExpireIn(CarbonInterval::minutes(config('auth.tokens.access.expiration')));
    }
}
