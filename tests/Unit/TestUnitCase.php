<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Exists;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

abstract class TestUnitCase extends TestCase
{
    protected function mockTransaction(): void
    {
        DB::shouldReceive('transaction')->once()->andReturnUsing(fn(\Closure $callback) => $callback());
    }

    /**
     * @template T of FormRequest
     *
     * @param class-string<T> $requestClass
     * @param array<string, mixed> $validated
     * @return T
     */
    protected function createFormRequestMock(string $requestClass, array $validated): FormRequest
    {
        $request = $requestClass::create('/', 'POST', $validated);
        $rules = collect($validated)->dot()->mapWithKeys(fn(mixed $value, string $key) => [$key => 'sometimes'])->all();
        $request->setValidator(Validator::make($validated, $rules));

        return $request;
    }

    /**
     * @template T of FormRequest
     *
     * @param class-string<T> $requestClass
     * @param array<string, mixed> $routeParams
     * @return T
     */
    protected function createRequestWithRouteParams(
        string $requestClass,
        string $method,
        string $uri,
        array $routeParams,
    ): FormRequest {
        $request = $requestClass::create('/fake', $method);
        $route = new Route($method, $uri, []);
        $route->bind($request);

        foreach ($routeParams as $name => $value) {
            $route->setParameter($name, $value);
        }

        $request->setRouteResolver(fn() => $route);

        return $request;
    }

    /**
     * @template T of Model
     *
     * @param  class-string<T>       $originalClassName
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $configuration
     * @return T&MockObject
     */
    protected function createConfiguredModelMock(
        string $originalClassName,
        array $attributes = [],
        array $configuration = [],
    ): MockObject {
        $mock = $this->createMock($originalClassName);

        $mock->method('__get')->willReturnCallback(
            fn(string $key) => $attributes[$key] ?? null,
        );

        foreach ($configuration as $method => $return) {
            $mock->method($method)->willReturn($return);
        }

        return $mock;
    }

    protected function containsExistsRule(mixed $rules): bool
    {
        if (!\is_array($rules)) {
            return false;
        }

        foreach ($rules as $rule) {
            if ($rule instanceof Exists) {
                return true;
            }
        }

        return false;
    }
}
