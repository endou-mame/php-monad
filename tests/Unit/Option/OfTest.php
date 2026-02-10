<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Option;

use DateTimeImmutable;
use DivisionByZeroError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\Provider\OptionProvider;
use EndouMame\PhpMonad\Tests\TestCase;

#[TestDox('Option - OfTest')]
#[CoversClass(Option::class)]
final class OfTest extends TestCase
{
    use OptionProvider;

    /**
     * @param Option<mixed> $expected
     */
    #[Test]
    #[TestDox('of test')]
    #[DataProvider('fromValueMatrix')]
    public function of(Option $expected, mixed $value, mixed $noneValue): void
    {
        Assert::assertEquals($expected, Option\of(static fn () => $value, $noneValue));
    }

    /**
     * @param Option<mixed> $expected
     */
    #[Test]
    #[TestDox('tryOf test')]
    #[DataProvider('fromValueMatrix')]
    public function tryOf(Option $expected, mixed $value, mixed $noneValue): void
    {
        Assert::assertEquals($expected, Option\tryOf(static fn () => $value, $noneValue));
    }

    #[Test]
    #[TestDox('ofDefaultToNull test')]
    public function ofDefaultToNull(): void
    {
        Assert::assertEquals(Option\none(), Option\of(static fn () => null));
        Assert::assertEquals(Option\some(1), Option\of(static fn () => 1));
    }

    #[Test]
    #[TestDox('tryOfDefaultToNull test')]
    public function tryOfDefaultToNull(): void
    {
        Assert::assertEquals(Option\none(), Option\tryOf(static fn () => null));
        Assert::assertEquals(Option\some(1), Option\tryOf(static fn () => 1));
    }

    #[Test]
    #[TestDox('tryOfExeptions test')]
    public function tryOfExeptions(): void
    {
        // @phpstan-ignore-next-line
        Assert::assertEquals(Option\none(), Option\tryOf(static fn () => new DateTimeImmutable('nope')));

        try {
            // @phpstan-ignore-next-line
            Option\tryOf(static fn () => 1 / 0);
            Assert::fail('An exception should have been thrown');
        } catch (DivisionByZeroError) {
        }
    }
}
