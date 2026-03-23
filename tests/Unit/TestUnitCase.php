<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

abstract class TestUnitCase extends TestCase
{
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
}
