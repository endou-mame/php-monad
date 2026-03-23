<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Option;

use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\Provider\ValueProvider;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use RuntimeException;

#[TestDox('Option - UnwrapTest')]
#[CoversClass(Option::class)]
final class UnwrapTest extends TestCase
{
    use ValueProvider;

    #[Test]
    #[TestDox('expectNone test')]
    public function expectNone(): void
    {
        $this->expectExceptionObject(new RuntimeException('This should fail'));

        Option\none()->expect('This should fail');
    }

    #[Test]
    #[TestDox('expectSome test')]
    #[DataProvider('values')]
    public function expectSome(mixed $value): void
    {
        Assert::assertSame($value, Option\some($value)->expect('This should succeed'));
    }

    #[Test]
    #[TestDox('unwrapNone test')]
    public function unwrapNone(): void
    {
        $this->expectExceptionObject(new RuntimeException('Unwrapping a `None` value'));

        Option\none()->unwrap();
    }

    #[Test]
    #[TestDox('unwrapSome test')]
    #[DataProvider('values')]
    public function unwrapSome(mixed $value): void
    {
        Assert::assertSame($value, Option\some($value)->unwrap());
    }

    #[Test]
    #[TestDox('unwrapOrNone test')]
    #[DataProvider('values')]
    public function unwrapOrNone(mixed $value): void
    {
        Assert::assertSame($value, Option\none()->unwrapOr($value));
    }

    #[Test]
    #[TestDox('unwrapOrSome test')]
    #[DataProvider('values')]
    public function unwrapOrSome(mixed $value): void
    {
        Assert::assertSame($value, Option\some($value)->unwrapOr(false));
    }

    #[Test]
    #[TestDox('unwrapOrElseNone test')]
    #[DataProvider('values')]
    public function unwrapOrElseNone(mixed $value): void
    {
        Assert::assertSame($value, Option\none()->unwrapOrElse(static fn () => $value));
    }

    #[Test]
    #[TestDox('unwrapOrElseSome test')]
    #[DataProvider('values')]
    public function unwrapOrElseSome(mixed $value): void
    {
        Assert::assertSame($value, Option\some($value)->unwrapOrElse(static fn () => false));
    }
}
