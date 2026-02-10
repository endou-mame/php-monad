<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;

#[TestDox('Result - MatchTest')]
#[CoversClass(Result::class)]
final class MatchTest extends TestCase
{
    /**
     * @return Result<int, string>
     */
    public function createResult(): Result
    {
        return Result\ok(42);
    }

    /**
     * @template T
     * @template E
     * @template U
     * @param Result<T, E> $result
     * @param U            $okValue       Value to return when Result is Ok
     * @param U            $errValue      Value to return when Result is Err
     * @param U            $expected      Expected outcome
     * @param array<T|E>   $expectedCalls Values that should be passed to the callbacks
     */
    #[Test]
    #[TestDox('match test')]
    #[DataProvider('matchMatrix')]
    public function match(
        Result $result,
        mixed $okValue,
        mixed $errValue,
        mixed $expected,
        array $expectedCalls
    ): void {
        $calls = [];

        $actual = $result->match(
            static function (mixed $value) use ($okValue, &$calls): mixed {
                $calls[] = $value;

                return $okValue;
            },
            static function (mixed $value) use ($errValue, &$calls): mixed {
                $calls[] = $value;

                return $errValue;
            }
        );

        Assert::assertSame($expected, $actual);
        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @return iterable<array{
     *   Result\Ok<int>|Result\Err<string>,
     *   string,
     *   string,
     *   string,
     *   array<int|string>
     * }>
     */
    public static function matchMatrix(): iterable
    {
        yield 'ok' => [
            Result\ok(42),
            'success',
            'failure',
            'success',
            [42],
        ];

        yield 'err' => [
            Result\err('error'),
            'success',
            'failure',
            'failure',
            ['error'],
        ];
    }
}
