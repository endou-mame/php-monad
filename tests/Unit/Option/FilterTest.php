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

#[TestDox('Option - FilterTest')]
#[CoversClass(Option::class)]
final class FilterTest extends TestCase
{
    /**
     * @template T
     * @param Option<T> $option
     * @param array<T>  $expectedCalls
     */
    #[Test]
    #[TestDox('filter test')]
    #[DataProvider('filterMatrix')]
    public function filter(Option $option, bool $filterResult, bool $expectNone, array $expectedCalls): void
    {
        $calls = [];

        Assert::assertSame(
            $expectNone ? Option\none() : $option,
            $option->filter(static function (mixed $value) use ($filterResult, &$calls): bool {
                $calls[] = $value;

                return $filterResult;
            }),
        );

        Assert::assertSame($expectedCalls, $calls);
    }

    // -------------------------------------------------------------------------
    // Data provider
    // -------------------------------------------------------------------------
    /**
     * @return iterable<array{
     *   Option<int>,
     *   bool,
     *   bool,
     *   array<int>
     * }>
     */
    public static function filterMatrix(): iterable
    {
        yield 'none-true' => [
            Option\none(),
            true,
            true,
            [],
        ];

        yield 'none-false' => [
            Option\none(),
            false,
            true,
            [],
        ];

        yield 'some-true' => [
            Option\some(5),
            true,
            false,
            [5],
        ];

        yield 'some-false' => [
            Option\some(42),
            false,
            true,
            [42],
        ];
    }
}
