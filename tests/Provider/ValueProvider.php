<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Provider;

use DateTimeImmutable;

trait ValueProvider
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public static function values(): iterable
    {
        yield 'null' => [null];
        yield 'empty' => [''];
        yield 'string' => ['example'];
        yield 'int' => [42];
        yield 'float' => [3.14];
        yield 'true' => [true];
        yield 'false' => [false];
        yield 'object' => [(object)[]];
        yield 'array' => [[]];
        yield 'datetime' => [new DateTimeImmutable()];
    }
}
