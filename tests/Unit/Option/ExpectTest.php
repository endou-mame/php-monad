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

#[TestDox('Option - ExpectTest')]
#[CoversClass(Option::class)]
final class ExpectTest extends TestCase
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
}
