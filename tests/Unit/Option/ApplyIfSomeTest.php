<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Option;

use Closure;
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Option - apply_if_some関数のテスト')]
#[CoversFunction('EndouMame\PhpMonad\Option\apply_if_some')]
final class ApplyIfSomeTest extends TestCase
{
    #[Test]
    #[TestDox('Someの場合、andThen操作を適用する')]
    public function applyIfSomeSome(): void
    {
        $option = Option\some(10);
        $fn = Option\apply_if_some(
            $option,
            static fn (int $optionValue): Closure => static fn (int $resultValue): Result\Ok => Result\ok($resultValue + $optionValue),
        );

        $result = $fn(Result\ok(32));

        Assert::assertTrue($result->isOk());
        Assert::assertSame(42, $result->unwrap());
    }

    #[Test]
    #[TestDox('Noneの場合、Resultをそのまま通す')]
    public function applyIfSomeNone(): void
    {
        /** @var Option<int> $option */
        $option = Option\none();
        $fn = Option\apply_if_some(
            $option,
            static fn (int $optionValue): Closure => static fn (int $resultValue): Result\Ok => Result\ok($resultValue + $optionValue),
        );

        $result = $fn(Result\ok(42));

        Assert::assertTrue($result->isOk());
        Assert::assertSame(42, $result->unwrap());
    }

    #[Test]
    #[TestDox('Someの場合でもErrはそのまま通す')]
    public function applyIfSomeSomeWithErr(): void
    {
        $option = Option\some(10);
        $fn = Option\apply_if_some(
            $option,
            static fn (int $optionValue): Closure => static fn (int $resultValue): Result\Ok => Result\ok($resultValue + $optionValue),
        );

        /** @phpstan-ignore argument.type */
        $result = $fn(Result\err('error'));

        Assert::assertTrue($result->isErr());
        Assert::assertSame('error', $result->unwrapErr());
    }

    #[Test]
    #[TestDox('Someの場合、関数がErrを返すとErrになる')]
    public function applyIfSomeSomeReturnsErr(): void
    {
        $option = Option\some('bad');
        $fn = Option\apply_if_some(
            $option,
            static fn (string $optionValue): Closure => static fn (int $resultValue): Result\Err => Result\err("invalid: {$optionValue}"),
        );

        $result = $fn(Result\ok(42));

        Assert::assertTrue($result->isErr());
        Assert::assertSame('invalid: bad', $result->unwrapErr());
    }
}
