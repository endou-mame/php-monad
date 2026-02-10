<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Option;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;

#[TestDox('Option - BooleanTest')]
#[CoversClass(Option::class)]
final class BooleanTest extends TestCase
{
    /**
     * @template T
     * @param Option<T> $left
     * @param Option<T> $right
     * @param Option<T> $expected
     */
    #[Test]
    #[TestDox('and test')]
    #[DataProvider('andMatrix')]
    public function and(Option $left, Option $right, Option $expected): void
    {
        Assert::assertSame($expected, $left->and($right));
    }

    /**
     * @template T
     * @param Option<T> $left
     * @param Option<T> $right
     * @param Option<T> $expected
     */
    #[Test]
    #[TestDox('andThen test')]
    #[DataProvider('andMatrix')]
    public function andThen(Option $left, Option $right, Option $expected): void
    {
        $calls = [];
        $expectedCalls = $left instanceof Option\None
            ? []
            : [$left->unwrap()];

        Assert::assertSame($expected, $left->andThen(static function (mixed $value) use ($right, &$calls): mixed {
            $calls[] = $value;

            return $right;
        }));

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @template T
     * @param Option<T> $left
     * @param Option<T> $right
     * @param Option<T> $expected
     */
    #[Test]
    #[TestDox('or test')]
    #[DataProvider('orMatrix')]
    public function or(Option $left, Option $right, Option $expected): void
    {
        Assert::assertSame($expected, $left->or($right));
    }

    /**
     * @template T
     * @param Option<T> $left
     * @param Option<T> $right
     * @param Option<T> $expected
     */
    #[Test]
    #[TestDox('orElse test')]
    #[DataProvider('orMatrix')]
    public function orElse(Option $left, Option $right, Option $expected): void
    {
        $calls = 0;
        $expectedCalls = $left instanceof Option\None
            ? 1
            : 0;

        Assert::assertSame($expected, $left->orElse(static function () use ($right, &$calls): mixed {
            ++$calls;

            return $right;
        }));

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @template T
     * @param Option<T> $left
     * @param Option<T> $right
     * @param Option<T> $expected
     */
    #[Test]
    #[TestDox('xor test')]
    #[DataProvider('xorMatrix')]
    public function xor(Option $left, Option $right, Option $expected): void
    {
        Assert::assertSame($expected, $left->xor($right));
    }

    // -------------------------------------------------------------------------
    // Data provider
    // -------------------------------------------------------------------------
    /**
     * @return iterable<array{
     *   left:Option<string>,
     *   right:Option<string>,
     *   expected:Option<string>
     * }>
     */
    public static function andMatrix(): iterable
    {
        $none = Option\none();
        $left = Option\some('left');
        $right = Option\some('right');

        yield 'none-none' => [
            'left'     => $none,
            'right'    => $none,
            'expected' => $none,
        ];

        yield 'none-some' => [
            'left'     => $none,
            'right'    => $right,
            'expected' => $none,
        ];

        yield 'some-some' => [
            'left'     => $left,
            'right'    => $right,
            'expected' => $right,
        ];

        yield 'some-none' => [
            'left'     => $left,
            'right'    => $none,
            'expected' => $none,
        ];
    }

    /**
     * @return iterable<array{
     *   left:Option<string>,
     *   right:Option<string>,
     *   expected:Option<string>
     * }>
     */
    public static function orMatrix(): iterable
    {
        $none = Option\none();
        $left = Option\some('left');
        $right = Option\some('right');

        yield 'none-none' => [
            'left'     => $none,
            'right'    => $none,
            'expected' => $none,
        ];

        yield 'none-some' => [
            'left'     => $none,
            'right'    => $right,
            'expected' => $right,
        ];

        yield 'some-some' => [
            'left'     => $left,
            'right'    => $right,
            'expected' => $left,
        ];

        yield 'some-none' => [
            'left'     => $left,
            'right'    => $none,
            'expected' => $left,
        ];
    }

    /**
     * @return iterable<array{
     *   left:Option<string>,
     *   right:Option<string>,
     *   expected:Option<string>
     * }>
     */
    public static function xorMatrix(): iterable
    {
        $none = Option\none();
        $left = Option\some('left');
        $right = Option\some('right');

        yield 'none-none' => [
            'left'     => $none,
            'right'    => $none,
            'expected' => $none,
        ];

        yield 'none-some' => [
            'left'     => $none,
            'right'    => $right,
            'expected' => $right,
        ];

        yield 'some-some' => [
            'left'     => $left,
            'right'    => $right,
            'expected' => $none,
        ];

        yield 'some-none' => [
            'left'     => $left,
            'right'    => $none,
            'expected' => $left,
        ];
    }
}
