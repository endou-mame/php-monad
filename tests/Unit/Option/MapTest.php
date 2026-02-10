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

#[TestDox('Option - MapTest')]
#[CoversClass(Option::class)]
final class MapTest extends TestCase
{
    /**
     * @template T
     * @template U
     * @param Option<T> $option
     * @param U         $mapResult
     * @param Option<U> $expected
     * @param array<T>  $expectedCalls
     */
    #[Test]
    #[TestDox('map test')]
    #[DataProvider('mapMatrix')]
    public function map(Option $option, mixed $mapResult, Option $expected, array $expectedCalls): void
    {
        $calls = [];

        Assert::assertEquals(
            $expected,
            $option->map(static function (mixed $value) use ($mapResult, &$calls): mixed {
                $calls[] = $value;

                return $mapResult;
            }),
        );

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @template T
     * @template U
     * @param Option<T> $option
     * @param U         $mapResult
     * @param U         $default
     * @param U         $expected
     * @param array<T>  $expectedCalls
     */
    #[Test]
    #[TestDox('mapOr test')]
    #[DataProvider('mapOrMatrix')]
    public function mapOr(
        Option $option,
        mixed $mapResult,
        mixed $default,
        mixed $expected,
        array $expectedCalls,
    ): void {
        $calls = [];

        Assert::assertEquals(
            $expected,
            $option->mapOr(static function (mixed $value) use ($mapResult, &$calls): mixed {
                $calls[] = $value;

                return $mapResult;
            }, $default),
        );

        Assert::assertSame($expectedCalls, $calls);
    }

    // -------------------------------------------------------------------------
    // Data provider
    // -------------------------------------------------------------------------
    /**
     * @return iterable<array{
     *   Option<int>,
     *   mixed,
     *   Option<string>,
     *   array<mixed>
     * }>
     */
    public static function mapMatrix(): iterable
    {
        yield 'none' => [
            Option\none(),
            'fish',
            Option\none(),
            [],
        ];

        yield 'some' => [
            Option\some(42),
            'fish',
            Option\some('fish'),
            [42],
        ];
    }

    /**
     * @return iterable<array{
     *   Option<int>,
     *   mixed,
     *   mixed,
     *   mixed,
     *   array<mixed>
     * }>
     */
    public static function mapOrMatrix(): iterable
    {
        yield 'none' => [
            Option\none(),
            'fish',
            'fishes',
            'fishes',
            [],
        ];

        yield 'some' => [
            Option\some(42),
            'fish',
            'fishes',
            'fish',
            [42],
        ];
    }
}
