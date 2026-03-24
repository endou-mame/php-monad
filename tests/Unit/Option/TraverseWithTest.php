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

#[TestDox('Option - traverse_with関数のテスト')]
#[CoversFunction('EndouMame\PhpMonad\Option\traverse_with')]
final class TraverseWithTest extends TestCase
{
    #[Test]
    #[TestDox('Someに対してパイプラインでResult返却関数を適用する')]
    public function traverseWithSome(): void
    {
        $fn = Option\traverse_with(static fn (int $x): Result\Ok => Result\ok($x * 2));

        $result = $fn(Option\some(42));

        Assert::assertTrue($result->isOk());
        Assert::assertSame(84, $result->unwrap());
    }

    #[Test]
    #[TestDox('Noneに対してパイプラインでok(null)を返す')]
    public function traverseWithNone(): void
    {
        $fn = Option\traverse_with(static fn (int $x): Result\Ok => Result\ok($x * 2));

        /** @var Option<int> $input */
        $input = Option\none();
        $result = $fn($input);

        Assert::assertTrue($result->isOk());
        Assert::assertNull($result->unwrap());
    }
}
