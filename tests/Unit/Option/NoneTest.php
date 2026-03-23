<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Option;

use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Option - NoneTest')]
#[CoversClass(Option::class)]
final class NoneTest extends TestCase
{
    #[Test]
    #[TestDox('noneIsASingleton test')]
    public function noneIsASingleton(): void
    {
        Assert::assertEquals(Option\none(), Option\none());

        Assert::assertSame(Option\none(), Option\none());
    }
}
