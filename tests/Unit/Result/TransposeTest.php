<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Result - transpose関数のテスト')]
#[CoversClass(Result::class)]
final class TransposeTest extends TestCase
{
    #[Test]
    #[TestDox('Ok(Some(_))をSome(Ok(_))に変換するtransposeのテスト')]
    public function transposeOkSome(): void
    {
        $result = Result\ok(Option\some(42));

        /** @phpstan-ignore-next-line */
        $option = Result\transpose($result);

        Assert::assertTrue($option->isSome());
        Assert::assertTrue($option->unwrap()->isOk());
        Assert::assertSame(42, $option->unwrap()->unwrap());
    }

    #[Test]
    #[TestDox('Ok(None)をNoneに変換するtransposeのテスト')]
    public function transposeOkNone(): void
    {
        $result = Result\ok(Option\none());

        /** @phpstan-ignore-next-line */
        $option = Result\transpose($result);

        Assert::assertTrue($option->isNone());
    }

    #[Test]
    #[TestDox('Err(_)をSome(Err(_))に変換するtransposeのテスト')]
    public function transposeErr(): void
    {
        $result = Result\err('error');

        /** @phpstan-ignore-next-line */
        $option = Result\transpose($result);

        Assert::assertTrue($option->isSome());
        Assert::assertTrue($option->unwrap()->isErr());
        Assert::assertSame('error', $option->unwrap()->unwrapErr());
    }
}
