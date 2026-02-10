<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use RuntimeException;
use Throwable;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;

#[TestDox('Result - fromThrowable メソッドのテスト')]
#[CoversClass(Result::class)]
final class FromThrowableTest extends TestCase
{
    #[Test]
    #[TestDox('成功するコールバックを持つfromThrowableのテスト')]
    public function fromThrowableWithSuccessfulCallback(): void
    {
        $callback = static fn () => 42;
        $errorHandler = static fn (Throwable $e) => $e->getMessage();

        $result = Result\fromThrowable($callback, $errorHandler);

        Assert::assertTrue($result->isOk());
        Assert::assertSame(42, $result->unwrap());
    }

    #[Test]
    #[TestDox('例外をスローするコールバックを持つfromThrowableのテスト')]
    public function fromThrowableWithExceptionThrowingCallback(): void
    {
        $callback = static fn () => throw new RuntimeException('Test exception');
        $errorHandler = static fn (Throwable $e) => $e->getMessage();

        $result = Result\fromThrowable($callback, $errorHandler);

        Assert::assertTrue($result->isErr());
        Assert::assertSame('Test exception', $result->unwrapErr());
    }
}
