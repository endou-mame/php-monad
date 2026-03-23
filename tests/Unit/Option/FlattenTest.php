<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Option;

use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Option - FlattenTest')]
#[CoversClass(Option::class)]
final class FlattenTest extends TestCase
{
    /**
     * @param Option<mixed>         $expected
     * @param Option<Option<mixed>> $option
     */
    #[Test]
    #[TestDox('flatten test')]
    #[DataProvider('provideFlattenCases')]
    public function flatten(Option $expected, Option $option): void
    {
        Assert::assertSame($expected, Option\flatten($option));
    }

    /**
     * @return Generator<string,Option\None[]|(Option\None|Option\Some<Option\None>)[]|(Option\Some<null>|Option\Some<Option\Some<null>>)[],mixed,void>
     */
    public static function provideFlattenCases(): iterable
    {
        $none = Option\none();

        yield 'none' => [$none, $none];

        yield 'some(none)' => [$none, Option\some($none)];

        $leaf = Option\some(null);

        yield 'some(some(…))' => [$leaf, Option\some($leaf)];
    }
}
