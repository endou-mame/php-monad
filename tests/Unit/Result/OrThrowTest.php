<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use RuntimeException;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;

#[TestDox('Result - OrThrow メソッドのテスト')]
#[CoversClass(Result::class)]
final class OrThrowTest extends TestCase
{
    #[Test]
    #[TestDox('Ok型の場合は例外を投げずそのインスタンスを返す')]
    public function okReturnsItself(): void
    {
        $ok = Result\ok(42);
        $exception = new RuntimeException('This exception should not be thrown');

        $result = $ok->orThrow($exception);

        Assert::assertSame($ok, $result);
        Assert::assertTrue($result->isOk());
        Assert::assertSame(42, $result->unwrap());
    }

    #[Test]
    #[TestDox('Err型の場合は指定された例外を投げる')]
    public function errThrowsException(): void
    {
        $err = Result\err('エラーが発生しました');
        $exception = new RuntimeException('エラーが投げられました');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('エラーが投げられました');

        $err->orThrow($exception);
    }

    #[Test]
    #[TestDox('Err型の場合は指定された任意のThrowableを投げる')]
    public function errThrowsCustomException(): void
    {
        $err = Result\err('エラーが発生しました');
        $exception = new Exception('カスタム例外が投げられました');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('カスタム例外が投げられました');

        $err->orThrow($exception);
    }
}
