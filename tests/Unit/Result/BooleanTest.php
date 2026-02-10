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

#[TestDox('Result - BooleanTest')]
#[CoversClass(Result::class)]
final class BooleanTest extends TestCase
{
    /**
     * @template T
     * @param Result<T, mixed> $left
     * @param Result<T, mixed> $right
     * @param Result<T, mixed> $expected
     */
    #[Test]
    #[TestDox('and test')]
    #[DataProvider('andMatrix')]
    public function and(Result $left, Result $right, Result $expected): void
    {
        Assert::assertEquals($expected, $result = $left->and($right));
    }

    /**
     * @template T
     * @param Result<T, mixed> $left
     * @param Result<T, mixed> $right
     * @param Result<T, mixed> $expected
     */
    #[Test]
    #[TestDox('andThen test')]
    #[DataProvider('andMatrix')]
    public function andThen(Result $left, Result $right, Result $expected): void
    {
        $calls = [];
        $expectedCalls = $left instanceof Result\Err
            ? []
            : [$left->unwrap()];

        Assert::assertEquals($expected, $result = $left->andThen(
            static function (mixed $value) use ($right, &$calls): mixed {
                $calls[] = $value;

                return $right;
            },
        ));

        if (!$left instanceof Result\Err) {
            Assert::assertSame($result, $right);
        }
    }

    /**
     * @template T
     * @param Result<T, mixed> $left
     * @param Result<T, mixed> $right
     * @param Result<T, mixed> $expected
     */
    #[Test]
    #[TestDox('or test')]
    #[DataProvider('orMatrix')]
    public function or(Result $left, Result $right, Result $expected): void
    {
        Assert::assertEquals($expected, $result = $left->or($right));
    }

    /**
     * @template T
     * @param Result<T, mixed> $left
     * @param Result<T, mixed> $right
     * @param Result<T, mixed> $expected
     */
    #[Test]
    #[TestDox('orElse test')]
    #[DataProvider('orMatrix')]
    public function orElse(Result $left, Result $right, Result $expected): void
    {
        $calls = 0;
        $expectedCalls = $left instanceof Result\Err
            ? 1
            : 0;

        Assert::assertEquals($expected, $result = $left->orElse(static function () use ($right, &$calls): mixed {
            ++$calls;

            return $right;
        }));

        if ($left instanceof Result\Err) {
            Assert::assertSame($result, $right);
        }
    }

    /**
     * @return iterable<array{
     *   left:Result\Ok<string>|Result\Err<string>,
     *   right:Result\Ok<string>|Result\Err<string>,
     *   expected:Result\Ok<string>|Result\Err<string>
     * }>
     */
    public static function andMatrix(): iterable
    {

        yield 'err-err' => [
            'left'     => Result\err('left'),
            'right'    => Result\err('left'),
            'expected' => Result\err('left'),
        ];

        yield 'err-ok' => [
            'left'     => Result\err('left'),
            'right'    => Result\ok('right'),
            'expected' => Result\err('left'),
        ];

        yield 'ok-ok' => [
            'left'     => Result\ok('left'),
            'right'    => Result\ok('right'),
            'expected' => Result\ok('right'),
        ];

        yield 'ok-err' => [
            'left'     => Result\ok('left'),
            'right'    => Result\err('right'),
            'expected' => Result\err('right'),
        ];
    }

    /**
     * @return iterable<array{
     *   left:Result\Ok<string>|Result\Err<string>,
     *   right:Result\Ok<string>|Result\Err<string>,
     *   expected:Result\Ok<string>|Result\Err<string>
     * }>
     */
    public static function orMatrix(): iterable
    {
        yield 'err-err' => [
            'left'     => Result\err('left'),
            'right'    => Result\err('right'),
            'expected' => Result\err('right'),
        ];

        yield 'err-ok' => [
            'left'     => Result\err('left'),
            'right'    => Result\ok('right'),
            'expected' => Result\ok('right'),
        ];

        yield 'ok-ok' => [
            'left'     => Result\ok('left'),
            'right'    => Result\ok('right'),
            'expected' => Result\ok('left'),
        ];

        yield 'ok-err' => [
            'left'     => Result\ok('left'),
            'right'    => Result\err('right'),
            'expected' => Result\ok('left'),
        ];
    }
}
