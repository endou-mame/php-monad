<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Option;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;

#[TestDox('Option - InterfaceTest')]
#[CoversClass(Option::class)]
final class InterfaceTest extends TestCase
{
    #[Test]
    #[TestDox('instanceOfOption test')]
    public function instanceOfOption(): void
    {
        Assert::assertInstanceOf(Option::class, Option\none());
        Assert::assertInstanceOf(Option::class, Option\some(null));
    }

    #[Test]
    #[TestDox('instanceOfNone test')]
    public function instanceOfNone(): void
    {
        Assert::assertInstanceOf(Option\None::class, Option\none());
        Assert::assertNotInstanceOf(Option\None::class, Option\some(null));
    }

    #[Test]
    #[TestDox('instanceOfSome test')]
    public function instanceOfSome(): void
    {
        Assert::assertNotInstanceOf(Option\Some::class, Option\none());
        Assert::assertInstanceOf(Option\Some::class, Option\some(null));
    }
}
