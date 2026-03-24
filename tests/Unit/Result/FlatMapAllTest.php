<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Result - flat_map_all関数のテスト')]
#[CoversClass(Result::class)]
final class FlatMapAllTest extends TestCase
{
    #[Test]
    #[TestDox('すべてがOkでコールバックがOkを返す場合')]
    public function flatMapAllAllOkReturnsOk(): void
    {
        $result = Result\flat_map_all(
            static fn (int $a, int $b): Result => Result\ok($a + $b),
            Result\ok(1),
            Result\ok(2),
        );

        Assert::assertTrue($result->isOk());
        Assert::assertSame(3, $result->unwrap());
    }

    #[Test]
    #[TestDox('すべてがOkでコールバックがErrを返す場合')]
    public function flatMapAllAllOkReturnsErr(): void
    {
        $result = Result\flat_map_all(
            static fn (int $a, int $b): Result => Result\err('callback error'),
            Result\ok(1),
            Result\ok(2),
        );

        Assert::assertTrue($result->isErr());
        Assert::assertSame('callback error', $result->unwrapErr());
    }

    #[Test]
    #[TestDox('Errが含まれる場合は最初のErrを返す')]
    public function flatMapAllWithErr(): void
    {
        $result = Result\flat_map_all(
            static fn (int $a, int $b): Result => Result\ok($a + $b),
            Result\ok(1),
            Result\err('error1'),
            Result\err('error2'),
        );

        Assert::assertTrue($result->isErr());
        Assert::assertSame('error1', $result->unwrapErr());
    }

    #[Test]
    #[TestDox('単一のOkでも動作する')]
    public function flatMapAllSingleOk(): void
    {
        $result = Result\flat_map_all(
            static fn (int $a): Result => Result\ok("value: {$a}"),
            Result\ok(42),
        );

        Assert::assertTrue($result->isOk());
        Assert::assertSame('value: 42', $result->unwrap());
    }

    #[Test]
    #[TestDox('単一のErrでも動作する')]
    public function flatMapAllSingleErr(): void
    {
        $result = Result\flat_map_all(
            static fn (int $a): Result => Result\ok("value: {$a}"),
            Result\err('error'),
        );

        Assert::assertTrue($result->isErr());
        Assert::assertSame('error', $result->unwrapErr());
    }
}
