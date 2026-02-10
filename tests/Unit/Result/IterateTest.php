<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;

use function iterator_to_array;

#[TestDox('Result - IterateTest')]
#[CoversClass(Result::class)]
final class IterateTest extends TestCase
{
    #[Test]
    #[TestDox('iterateResults test')]
    public function iterateResults(): void
    {
        Assert::assertIsIterable($result = Result\err(null));

        Assert::assertSame([], iterator_to_array($result = Result\err(null)));

        Assert::assertIsIterable($result = Result\ok(42));

        Assert::assertSame([42], iterator_to_array($result = Result\ok(42)));
    }
}
