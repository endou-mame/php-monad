<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\Provider\ValueProvider;
use EndouMame\PhpMonad\Tests\TestCase;

#[TestDox('Result - IsTest')]
#[CoversClass(Result::class)]
final class IsTest extends TestCase
{
    use ValueProvider;

    #[Test]
    #[TestDox('isOk test')]
    #[DataProvider('values')]
    public function isOk(mixed $value): void
    {
        $result = Result\ok($value);
        Assert::assertTrue($result->isOk());

        $result = Result\err($value);
        Assert::assertFalse($result->isOk());
    }

    #[Test]
    #[TestDox('isErr test')]
    #[DataProvider('values')]
    public function isErr(mixed $value): void
    {
        $result = Result\ok($value);
        Assert::assertFalse($result->isErr());

        $result = Result\err($value);
        Assert::assertTrue($result->isErr());
    }

    #[Test]
    #[TestDox('isOkAnd test')]
    #[DataProvider('values')]
    public function isOkAnd(mixed $value): void
    {
        $result = Result\ok($value);

        Assert::assertTrue($result->isOkAnd(static fn (mixed $v) => $v === $value));
        Assert::assertFalse($result->isOkAnd(static fn (mixed $v) => $v !== $value));

        $result = Result\err($value);
        Assert::assertFalse($result->isOkAnd(static fn (mixed $v) => Assert::fail('predicate should be called')));
        Assert::assertFalse($result->isOkAnd(static fn (mixed $v) => Assert::fail('predicate should be called')));
    }

    #[Test]
    #[TestDox('isErrAnd test')]
    #[DataProvider('values')]
    public function isErrAnd(mixed $value): void
    {
        $result = Result\ok($value);
        Assert::assertFalse($result->isErrAnd(static fn (mixed $v) => Assert::fail('predicate should be called')));
        Assert::assertFalse($result->isErrAnd(static fn (mixed $v) => Assert::fail('predicate should be called')));

        $result = Result\err($value);

        Assert::assertTrue($result->isErrAnd(static fn (mixed $v) => $v === $value));
        Assert::assertFalse($result->isErrAnd(static fn (mixed $v) => $v !== $value));
    }
}
