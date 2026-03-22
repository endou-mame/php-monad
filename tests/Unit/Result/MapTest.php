<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Result - MapTest')]
#[CoversClass(Result::class)]
final class MapTest extends TestCase
{
    /**
     * @template T
     * @template U
     * @param Result<T, null> $result
     * @param U               $mapResult
     * @param Result<U, null> $expected
     * @param array<T>        $expectedCalls
     */
    #[Test]
    #[TestDox('map test')]
    #[DataProvider('provideMapCases')]
    public function map(Result $result, mixed $mapResult, Result $expected, array $expectedCalls): void
    {
        $calls = [];

        Assert::assertEquals(
            $expected,
            $result2 = $result->map(static function (mixed $value) use ($mapResult, &$calls): mixed {
                $calls[] = $value;

                return $mapResult;
            }),
        );

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @return iterable<array{
     *   Result\Ok<int>|Result\Err<null>,
     *   string,
     *   Result\Ok<string>|Result\Err<null>,
     *   array<int>
     * }>
     */
    public static function provideMapCases(): iterable
    {
        yield 'err' => [
            Result\err(null),
            'fish',
            Result\err(null),
            [],
        ];

        yield 'ok' => [
            Result\ok(42),
            'fish',
            Result\ok('fish'),
            [42],
        ];
    }

    /**
     * @template T
     * @template U
     * @param Result<T, null> $result
     * @param U               $mapResult
     * @param Result<U, null> $expected
     * @param array<T>        $expectedCalls
     */
    #[Test]
    #[TestDox('mapErr test')]
    #[DataProvider('provideMapErrCases')]
    public function mapErr(Result $result, mixed $mapResult, Result $expected, array $expectedCalls): void
    {
        $calls = [];

        Assert::assertEquals(
            $expected,
            $result2 = $result->mapErr(static function (mixed $value) use ($mapResult, &$calls): mixed {
                $calls[] = $value;

                return $mapResult;
            }),
        );

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @return iterable<array{
     *   Result\Ok<null>|Result\Err<int>,
     *   string,
     *   Result\Ok<null>|Result\Err<string>,
     *   array<int>
     * }>
     */
    public static function provideMapErrCases(): iterable
    {
        yield 'ok' => [
            Result\ok(null),
            'fish',
            Result\ok(null),
            [],
        ];

        yield 'err' => [
            Result\err(42),
            'fish',
            Result\err('fish'),
            [42],
        ];
    }

    /**
     * @template T
     * @template U
     * @param Result<T, null> $result
     * @param U               $mapResult
     * @param U               $default
     * @param U               $expected
     * @param array<T>        $expectedCalls
     */
    #[Test]
    #[TestDox('mapOr test')]
    #[DataProvider('provideMapOrCases')]
    public function mapOr(
        Result $result,
        mixed $mapResult,
        mixed $default,
        mixed $expected,
        array $expectedCalls,
    ): void {
        $calls = [];

        Assert::assertEquals(
            $expected,
            $result->mapOr(static function (mixed $value) use ($mapResult, &$calls): mixed {
                $calls[] = $value;

                return $mapResult;
            }, $default),
        );

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @return iterable<array{
     *   Result\Ok<int>|Result\Err<null>,
     *   string,
     *   string,
     *   string,
     *   array<int>
     * }>
     */
    public static function provideMapOrCases(): iterable
    {
        yield 'err' => [
            Result\err(null),
            'fish',
            'fishes',
            'fishes',
            [],
        ];

        yield 'ok' => [
            Result\ok(42),
            'fish',
            'fishes',
            'fish',
            [42],
        ];
    }
}
