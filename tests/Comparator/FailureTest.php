<?php

declare(strict_types=1);

namespace Tests\Comparator;

use DragonCode\RuntimeComparison\Exceptions\ValueIsNotCallableException;
use Tests\TestCase;
use TypeError;

class FailureTest extends TestCase
{
    public function testAsProperties(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('must be of type callable|array, int given');

        $this->comparator()->compare(123);
    }

    public function testAsArray(): void
    {
        $this->expectException(ValueIsNotCallableException::class);
        $this->expectExceptionMessage('The array value must be of type callable, integer given.');

        $this->comparator()->compare([
            'first'  => 123,
            'second' => 123,
        ]);
    }

    public function testAsPropertiesWithIterations(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('must be of type callable|array, int given');

        $this->comparator()->iterations(5)->compare(123);
    }

    public function testAsArrayWithIterations(): void
    {
        $this->expectException(ValueIsNotCallableException::class);
        $this->expectExceptionMessage('The array value must be of type callable, integer given.');

        $this->comparator()->iterations(5)->compare([
            'first'  => 123,
            'second' => 123,
        ]);
    }

    public function testAsPropertiesWithoutData(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('must be of type callable|array, int given');

        $this->comparator()->withoutData()->compare(123);
    }

    public function testAsArrayWithoutData(): void
    {
        $this->expectException(ValueIsNotCallableException::class);
        $this->expectExceptionMessage('The array value must be of type callable, integer given.');

        $this->comparator()->withoutData()->compare([
            'first'  => 123,
            'second' => 123,
        ]);
    }
}
