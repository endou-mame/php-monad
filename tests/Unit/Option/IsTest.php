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

#[TestDox('Option - IsTest')]
#[CoversClass(Option::class)]
final class IsTest extends TestCase
{
    use ValueProvider;

    #[Test]
    #[TestDox('isSome test')]
    #[DataProvider('values')]
    public function isSome(mixed $value): void
    {
        /** @var Option<mixed> $option */
        $option = Option\some($value);

        Assert::assertTrue($option->isSome());

        /** @var Option<never> $option */
        $option = Option\none();

        Assert::assertFalse($option->isSome());
    }

    #[Test]
    #[TestDox('isNone test')]
    #[DataProvider('values')]
    public function isNone(mixed $value): void
    {
        /** @var Option<mixed> $option */
        $option = Option\some($value);

        Assert::assertFalse($option->isNone());

        /** @var Option<never> $option */
        $option = Option\none();

        Assert::assertTrue($option->isNone());
    }

    #[Test]
    #[TestDox('isSomeAnd test')]
    #[DataProvider('values')]
    public function isSomeAnd(mixed $value): void
    {
        /** @var Option<mixed> $option */
        $option = Option\some($value);

        Assert::assertTrue($option->isSomeAnd(static fn (mixed $v) => $v === $value));
        Assert::assertFalse($option->isSomeAnd(static fn (mixed $v) => $v !== $value));

        /** @var Option<never> $option */
        $option = Option\none();

        Assert::assertFalse($option->isSomeAnd(static fn (mixed $v) => Assert::fail('predicate should be called')));
        Assert::assertFalse($option->isSomeAnd(static fn (mixed $v) => Assert::fail('predicate should be called')));
    }
}
