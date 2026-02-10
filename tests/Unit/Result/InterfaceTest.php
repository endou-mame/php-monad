<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;

#[TestDox('Result - InterfaceTest')]
#[CoversClass(Result::class)]
final class InterfaceTest extends TestCase
{
    #[Test]
    #[TestDox('instanceOfResult test')]
    public function instanceOfResult(): void
    {
        Assert::assertInstanceOf(Result::class, $result = Result\err(null));

        Assert::assertInstanceOf(Result::class, $result = Result\ok(null));
    }

    #[Test]
    #[TestDox('instanceOfErr test')]
    public function instanceOfErr(): void
    {
        Assert::assertInstanceOf(Result\Err::class, $result = Result\err(null));

        Assert::assertNotInstanceOf(Result\Err::class, $result = Result\ok(null));
    }

    #[Test]
    #[TestDox('instanceOfOk test')]
    public function instanceOfOk(): void
    {
        Assert::assertNotInstanceOf(Result\Ok::class, $result = Result\err(null));

        Assert::assertInstanceOf(Result\Ok::class, $result = Result\ok(null));
    }
}
