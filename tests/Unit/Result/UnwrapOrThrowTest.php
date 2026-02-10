<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;

use function EndouMame\PhpMonad\Result\err;
use function EndouMame\PhpMonad\Result\ok;

#[TestDox('Option - UnwrapOrThrowTest')]
#[CoversClass(Result::class)]
final class UnwrapOrThrowTest extends TestCase
{
    #[Test]
    #[TestDox('ok値の場合は値を返す')]
    public function ok値の場合は値を返す(): void
    {
        $value = 'test';
        $result = ok($value);
        $exception = new Exception('This exception should not be thrown');

        Assert::assertSame($value, $result->unwrapOrThrow($exception));
    }

    #[Test]
    #[TestDox('err値の場合は指定した例外をスローする')]
    public function err値の場合は指定した例外をスローする(): void
    {
        $result = err('error value');
        $message = 'Custom exception message';
        $exception = new RuntimeException($message);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($message);

        $result->unwrapOrThrow($exception);
    }

    #[Test]
    #[TestDox('err値の場合はカスタム例外をスローできる')]
    public function err値の場合はカスタム例外をスローできる(): void
    {
        $result = err('error value');
        $customException = new class ('Custom exception') extends Exception {
        };

        $this->expectException($customException::class);
        $this->expectExceptionMessage('Custom exception');

        $result->unwrapOrThrow($customException);
    }
}
