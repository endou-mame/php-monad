<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Option;

use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\Provider\OptionProvider;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Option - FromValueTest')]
#[CoversClass(Option::class)]
final class FromValueTest extends TestCase
{
    use OptionProvider;

    /**
     * @param Option<mixed> $expected
     */
    #[Test]
    #[TestDox('fromValue test')]
    #[DataProvider('fromValueMatrix')]
    public function fromValue(Option $expected, mixed $value, mixed $noneValue): void
    {
        Assert::assertEquals($expected, Option\fromValue($value, $noneValue));
    }

    #[Test]
    #[TestDox('fromValueDefaultToNull test')]
    public function fromValueDefaultToNull(): void
    {
        Assert::assertEquals(Option\none(), Option\fromValue(null));
        Assert::assertEquals(Option\some(1), Option\fromValue(1));
    }
}
