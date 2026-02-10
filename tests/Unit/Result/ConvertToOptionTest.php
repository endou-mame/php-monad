<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;

#[TestDox('Result - ConvertToOptionTest')]
#[CoversClass(Result::class)]
final class ConvertToOptionTest extends TestCase
{
    /**
     * @param Result<mixed, mixed> $result
     * @param Option<mixed>        $expected
     */
    #[Test]
    #[TestDox('ok test')]
    #[DataProvider('okMatrix')]
    public function ok(Result $result, Option $expected): void
    {
        Assert::assertEquals($expected, $result->ok());
    }

    /**
     * @param Result<mixed, mixed> $result
     * @param Option<mixed>        $expected
     */
    #[Test]
    #[TestDox('err test')]
    #[DataProvider('errMatrix')]
    public function err(Result $result, Option $expected): void
    {
        Assert::assertEquals($expected, $result->err());
    }

    /**
     * @return iterable<array{
     *  0:Result\Err<string>|Result\Ok<int>,
     *  1:Option\None|Option\Some<int>
     * }>
     */
    public static function okMatrix(): iterable
    {
        yield 'err' => [
            Result\err("Don't panic !"),
            Option\none(),
        ];

        yield 'ok' => [
            Result\ok(42),
            Option\some(42),
        ];
    }

    /**
     * @return iterable<array{
     *  0:Result\Err<string>|Result\Ok<int>,
     *  1:Option\Some<string>|Option\None
     * }>
     */
    public static function errMatrix(): iterable
    {
        yield 'err' => [
            Result\err("Don't panic !"),
            Option\some("Don't panic !"),
        ];

        yield 'ok' => [
            Result\ok(42),
            Option\none(),
        ];
    }
}
