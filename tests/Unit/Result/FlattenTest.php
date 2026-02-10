<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;

#[TestDox('Result - FlattenTest')]
#[CoversClass(Result::class)]
final class FlattenTest extends TestCase
{
    /**
     * @param Result<mixed, null>               $expected
     * @param Result<Result<mixed, null>, null> $result
     */
    #[Test]
    #[TestDox('flatten test')]
    #[DataProvider('flattenMatrix')]
    public function flatten(Result $expected, Result $result): void
    {
        Assert::assertEquals($expected, $result2 = Result\flatten($result));
    }

    /**
     * @return Generator<string|string|string,Result\Err<null>[]|(Result\Err<null>|Result\Ok<Result\Err<null>>)[]|(Result\Ok<null>|Result\Ok<Result\Ok<null>>)[],mixed,void>
     */
    public static function flattenMatrix(): Generator
    {
        yield 'err' => [Result\err(null), Result\err(null)];

        yield 'ok(err)' => [Result\err(null), Result\ok(Result\err(null))];

        yield 'ok(ok(…))' => [Result\ok(null), Result\ok(Result\ok(null))];
    }
}
