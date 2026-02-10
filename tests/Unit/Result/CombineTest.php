<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;

#[TestDox('Result - combine関数のテスト')]
#[CoversClass(Result::class)]
final class CombineTest extends TestCase
{
    #[Test]
    #[TestDox('すべてがOkの場合はOk(true)を返すcombineのテスト')]
    public function combineAllOk(): void
    {
        $results = [
            Result\ok(1),
            Result\ok('test'),
            Result\ok(true),
        ];

        $result = Result\combine(...$results);

        Assert::assertTrue($result->isOk());
        Assert::assertTrue($result->unwrap());
    }

    #[Test]
    #[TestDox('1つでもErrがある場合はErr(list<E>)を返すcombineのテスト')]
    public function combineWithErrors(): void
    {
        $results = [
            Result\ok(1),
            Result\err('error1'),
            Result\ok(true),
            Result\err('error2'),
            Result\ok('test'),
        ];

        $result = Result\combine(...$results);

        Assert::assertTrue($result->isErr());
        $errors = $result->unwrapErr();
        Assert::assertIsArray($errors);
        Assert::assertCount(2, $errors);
        // エラーの順序は配列のフィルタリング方法によって決まる
        // 2つのエラーが含まれていることを確認
        Assert::assertTrue($errors[0] === 'error1');
        Assert::assertTrue($errors[1] === 'error2');
    }
}
