<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit;

use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

use function EndouMame\PhpMonad\pipe;

#[TestDox('Pipeline - pipe()')]
#[CoversFunction('EndouMame\PhpMonad\pipe')]
final class PipelineTest extends TestCase
{
    #[Test]
    #[TestDox('pipe chains Result operations')]
    public function pipeResult(): void
    {
        $result = pipe(
            Result\ok(10),
            Result\map(static fn (int $x): int => $x * 2),
            Result\map(static fn (int $x): int => $x + 1),
            Result\unwrapOr(0),
        );

        Assert::assertSame(21, $result);
    }

    #[Test]
    #[TestDox('pipe short-circuits on Err')]
    public function pipeResultErr(): void
    {
        $result = pipe(
            Result\ok(10),
            Result\andThen(static fn (int $x): Result\Err => Result\err('fail')),
            Result\map(static fn (int $x): int => $x * 100),
            Result\unwrapOr(0),
        );

        Assert::assertSame(0, $result);
    }

    #[Test]
    #[TestDox('pipe chains Option operations')]
    public function pipeOption(): void
    {
        $result = pipe(
            Option\some(10),
            Option\map(static fn (int $x): int => $x * 3),
            Option\filter(static fn (int $x): bool => $x > 20),
            Option\unwrapOr(0),
        );

        Assert::assertSame(30, $result);
    }

    #[Test]
    #[TestDox('pipe short-circuits on None')]
    public function pipeOptionNone(): void
    {
        $result = pipe(
            Option\some(5),
            Option\filter(static fn (int $x): bool => $x > 10),
            Option\map(static fn (int $x): int => $x * 100),
            Option\unwrapOr(0),
        );

        Assert::assertSame(0, $result);
    }

    #[Test]
    #[TestDox('pipe converts Option to Result')]
    public function pipeOptionToResult(): void
    {
        $result = pipe(
            Option\some(42),
            Option\okOr('not found'),
            Result\map(static fn (int $x): int => $x + 1),
            Result\unwrapOr(0),
        );

        Assert::assertSame(43, $result);
    }

    #[Test]
    #[TestDox('pipe returns value with no callbacks')]
    public function pipeNoCallbacks(): void
    {
        Assert::assertSame(42, pipe(42));
    }

    #[Test]
    #[TestDox('pipe with full ROP chain including error handling')]
    public function pipeFullRop(): void
    {
        /** @var list<string> $log */
        $log = [];

        $result = pipe(
            Result\ok('hello'),
            Result\inspect(static function (string $x) use (&$log): void {
                $log[] = "input: {$x}";
            }),
            Result\map(static fn (string $x): string => mb_strtoupper($x)),
            Result\andThen(static function (string $x): Result {
                return mb_strlen($x) > 3 ? Result\ok($x) : Result\err('too short');
            }),
            Result\inspectErr(static function (string $e) use (&$log): void {
                $log[] = "error: {$e}";
            }),
            Result\unwrapOr('DEFAULT'),
        );

        Assert::assertSame('HELLO', $result);
        Assert::assertSame(['input: hello'], $log);
    }
}
