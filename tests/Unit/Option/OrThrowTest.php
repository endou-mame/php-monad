<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Option;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use RuntimeException;
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;

#[TestDox('Option - OrThrow メソッドのテスト')]
#[CoversClass(Option::class)]
final class OrThrowTest extends TestCase
{
    #[Test]
    #[TestDox('Some型の場合は例外を投げずそのインスタンスを返す')]
    public function someReturnsItself(): void
    {
        $some = Option\some(42);
        $exception = new RuntimeException('This exception should not be thrown');

        $result = $some->orThrow($exception);

        Assert::assertSame($some, $result);
        Assert::assertTrue($result->isSome()); // @phpstan-ignore-line
        Assert::assertSame(42, $result->unwrap());
    }

    #[Test]
    #[TestDox('None型の場合は指定された例外を投げる')]
    public function noneThrowsException(): void
    {
        $none = Option\none();
        $exception = new RuntimeException('エラーが投げられました');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('エラーが投げられました');

        $none->orThrow($exception);
    }

    #[Test]
    #[TestDox('None型の場合は指定された任意のThrowableを投げる')]
    public function noneThrowsCustomException(): void
    {
        $none = Option\none();
        $exception = new Exception('カスタム例外が投げられました');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('カスタム例外が投げられました');

        $none->orThrow($exception);
    }
}
