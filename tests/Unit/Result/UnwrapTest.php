<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Provider\ValueProvider;
use EndouMame\PhpMonad\Tests\TestCase;
use LogicException;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use RuntimeException;

#[TestDox('Result - UnwrapTest')]
#[CoversClass(Result::class)]
final class UnwrapTest extends TestCase
{
    use ValueProvider;

    #[Test]
    #[TestDox('expectErr test')]
    public function expectErr(): void
    {
        $this->expectExceptionObject(new RuntimeException('This should fail'));

        Result\err(null)->expect('This should fail');
    }

    #[Test]
    #[TestDox('expectOk test')]
    #[DataProvider('values')]
    public function expectOk(mixed $value): void
    {
        Assert::assertSame($value, Result\ok($value)->expect('This should succeed'));
    }

    #[Test]
    #[TestDox('unwrapErr test')]
    public function unwrapErr(): void
    {
        $this->expectExceptionObject(new RuntimeException('Unwrapping `Err`: N;'));

        Result\err(null)->unwrap();
    }

    #[Test]
    #[TestDox('unwrapErrException test')]
    public function unwrapErrException(): void
    {
        $ex = new LogicException('Something went wrong');

        $this->expectExceptionObject($ex);

        Result\err($ex)->unwrap();
    }

    #[Test]
    #[TestDox('unwrapOk test')]
    #[DataProvider('values')]
    public function unwrapOk(mixed $value): void
    {
        Assert::assertSame($value, Result\ok($value)->unwrap());
    }

    #[Test]
    #[TestDox('unwrapOrErr test')]
    #[DataProvider('values')]
    public function unwrapOrErr(mixed $value): void
    {
        Assert::assertSame($value, Result\err(null)->unwrapOr($value));
    }

    #[Test]
    #[TestDox('unwrapOrOk test')]
    #[DataProvider('values')]
    public function unwrapOrOk(mixed $value): void
    {
        Assert::assertSame($value, Result\ok($value)->unwrapOr(false));
    }

    #[Test]
    #[TestDox('unwrapOrElseErr test')]
    #[DataProvider('values')]
    public function unwrapOrElseErr(mixed $value): void
    {
        Assert::assertSame($value, Result\err(null)->unwrapOrElse(static fn () => $value));
    }

    #[Test]
    #[TestDox('unwrapOrElseOk test')]
    #[DataProvider('values')]
    public function unwrapOrElseOk(mixed $value): void
    {
        Assert::assertSame($value, Result\ok($value)->unwrapOrElse(static fn () => false));
    }
}
