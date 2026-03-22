<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Option;

use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Option - ConvertToResultTest')]
#[CoversClass(Option::class)]
final class ConvertToResultTest extends TestCase
{
    /**
     * @param Option<mixed>        $option
     * @param Result<mixed, mixed> $expected
     */
    #[Test]
    #[TestDox('okOr test')]
    #[DataProvider('okOrMatrix')]
    public function okOr(Option $option, mixed $err, Result $expected): void
    {
        Assert::assertEquals($expected, $result = $option->okOr($err));
    }

    /**
     * @param Option<mixed>        $option
     * @param Result<mixed, mixed> $expected
     */
    #[Test]
    #[TestDox('okOrElse test')]
    #[DataProvider('okOrMatrix')]
    public function okOrElse(Option $option, mixed $err, Result $expected, int $expectedCalls): void
    {
        $calls = 0;

        Assert::assertEquals($expected, $result = $option->okOrElse(static function () use ($err, &$calls): mixed {
            ++$calls;

            return $err;
        }));

        Assert::assertSame($expectedCalls, $calls);
    }

    // -------------------------------------------------------------------------
    // Data provider
    // -------------------------------------------------------------------------
    /**
     * @return iterable<array{
     *   Option<int>,
     *   string,
     *   Result<int, string>,
     *   int
     * }>
     */
    public static function okOrMatrix(): iterable
    {
        yield 'none' => [
            Option\none(),
            "Don't panic !",
            Result\err("Don't panic !"),
            1,
        ];

        yield 'some' => [
            Option\some(42),
            "Don't panic !",
            Result\ok(42),
            0,
        ];
    }
}
