<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Option;

use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Option - traverse関数のテスト')]
#[CoversFunction('EndouMame\PhpMonad\Option\traverse')]
final class TraverseTest extends TestCase
{
    #[Test]
    #[TestDox('Someに対してResult返却関数を適用し、Ok結果を返す')]
    public function traverseSomeReturnsOk(): void
    {
        $option = Option\some(42);

        $result = Option\traverse($option, static fn (int $x): Result\Ok => Result\ok($x * 2));

        Assert::assertTrue($result->isOk());
        Assert::assertSame(84, $result->unwrap());
    }

    #[Test]
    #[TestDox('Someに対してResult返却関数を適用し、Err結果を返す')]
    public function traverseSomeReturnsErr(): void
    {
        $option = Option\some('invalid');

        $result = Option\traverse($option, static fn (string $x): Result\Err => Result\err("error: {$x}"));

        Assert::assertTrue($result->isErr());
        Assert::assertSame('error: invalid', $result->unwrapErr());
    }

    #[Test]
    #[TestDox('Noneに対してはok(null)を返す')]
    public function traverseNoneReturnsOkNull(): void
    {
        /** @var Option<int> $option */
        $option = Option\none();

        $result = Option\traverse($option, static fn (int $x): Result\Ok => Result\ok($x * 2));

        Assert::assertTrue($result->isOk());
        Assert::assertNull($result->unwrap());
    }
}
