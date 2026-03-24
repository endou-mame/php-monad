<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Option;

use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Option - ok_or関数のテスト')]
#[CoversFunction('EndouMame\PhpMonad\Option\ok_or')]
final class OkOrFunctionTest extends TestCase
{
    #[Test]
    #[TestDox('SomeをOkに変換する')]
    public function okOrSome(): void
    {
        $option = Option\some(42);

        $result = Option\ok_or($option, 'not found');

        Assert::assertEquals(Result\ok(42), $result);
    }

    #[Test]
    #[TestDox('NoneをErrに変換する')]
    public function okOrNone(): void
    {
        /** @var Option<int> $option */
        $option = Option\none();

        $result = Option\ok_or($option, 'not found');

        Assert::assertEquals(Result\err('not found'), $result);
    }
}
