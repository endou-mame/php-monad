<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Result - map_all関数のテスト')]
#[CoversClass(Result::class)]
final class MapAllTest extends TestCase
{
    #[Test]
    #[TestDox('すべてがOkの場合はコールバックの結果をOkで返す')]
    public function mapAllAllOk(): void
    {
        $result = Result\map_all(
            static fn (int $a, int $b, int $c): int => $a + $b + $c,
            Result\ok(1),
            Result\ok(2),
            Result\ok(3),
        );

        Assert::assertTrue($result->isOk());
        Assert::assertSame(6, $result->unwrap());
    }

    #[Test]
    #[TestDox('Errが含まれる場合は最初のErrを返す')]
    public function mapAllWithErr(): void
    {
        $result = Result\map_all(
            static fn (int $a, int $b): int => $a + $b,
            Result\ok(1),
            Result\err('error1'),
            Result\err('error2'),
        );

        Assert::assertTrue($result->isErr());
        Assert::assertSame('error1', $result->unwrapErr());
    }

    #[Test]
    #[TestDox('単一のOkでも動作する')]
    public function mapAllSingleOk(): void
    {
        $result = Result\map_all(
            static fn (int $a): string => "value: {$a}",
            Result\ok(42),
        );

        Assert::assertTrue($result->isOk());
        Assert::assertSame('value: 42', $result->unwrap());
    }

    #[Test]
    #[TestDox('単一のErrでも動作する')]
    public function mapAllSingleErr(): void
    {
        $result = Result\map_all(
            static fn (int $a): string => "value: {$a}",
            Result\err('error'),
        );

        Assert::assertTrue($result->isErr());
        Assert::assertSame('error', $result->unwrapErr());
    }
}
