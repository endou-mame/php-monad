<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use RuntimeException;

#[TestDox('Result - Pipeline Functions')]
#[CoversFunction('EndouMame\PhpMonad\Result\map')]
#[CoversFunction('EndouMame\PhpMonad\Result\mapErr')]
#[CoversFunction('EndouMame\PhpMonad\Result\andThen')]
#[CoversFunction('EndouMame\PhpMonad\Result\orElse')]
#[CoversFunction('EndouMame\PhpMonad\Result\inspect')]
#[CoversFunction('EndouMame\PhpMonad\Result\inspectErr')]
#[CoversFunction('EndouMame\PhpMonad\Result\unwrapOr')]
#[CoversFunction('EndouMame\PhpMonad\Result\unwrapOrElse')]
#[CoversFunction('EndouMame\PhpMonad\Result\expect')]
final class PipelineTest extends TestCase
{
    #[Test]
    #[TestDox('map transforms Ok value')]
    public function mapOk(): void
    {
        $fn = Result\map(static fn (int $x): int => $x * 2);

        /** @var Result<int, string> $input */
        $input = Result\ok(42);

        Assert::assertEquals(Result\ok(84), $fn($input));
    }

    #[Test]
    #[TestDox('map passes through Err')]
    public function mapErr(): void
    {
        $fn = Result\map(static fn (int $x): int => $x * 2);

        /** @var Result<int, string> $input */
        $input = Result\err('error');

        Assert::assertEquals(Result\err('error'), $fn($input));
    }

    #[Test]
    #[TestDox('mapErr transforms Err value')]
    public function mapErrTransforms(): void
    {
        $fn = Result\mapErr(static fn (string $e): string => "prefix: {$e}");

        /** @var Result<int, string> $input */
        $input = Result\err('error');

        Assert::assertEquals(Result\err('prefix: error'), $fn($input));
    }

    #[Test]
    #[TestDox('mapErr passes through Ok')]
    public function mapErrPassesOk(): void
    {
        $fn = Result\mapErr(static fn (string $e): string => "prefix: {$e}");

        /** @var Result<int, string> $input */
        $input = Result\ok(42);

        Assert::assertEquals(Result\ok(42), $fn($input));
    }

    #[Test]
    #[TestDox('andThen chains on Ok')]
    public function andThenOk(): void
    {
        $fn = Result\andThen(static fn (int $x): Result\Ok => Result\ok($x + 1));

        /** @var Result<int, string> $input */
        $input = Result\ok(42);

        Assert::assertEquals(Result\ok(43), $fn($input));
    }

    #[Test]
    #[TestDox('andThen passes through Err')]
    public function andThenErr(): void
    {
        $fn = Result\andThen(static fn (int $x): Result\Ok => Result\ok($x + 1));

        /** @var Result<int, string> $input */
        $input = Result\err('error');

        Assert::assertEquals(Result\err('error'), $fn($input));
    }

    #[Test]
    #[TestDox('orElse recovers from Err')]
    public function orElseErr(): void
    {
        $fn = Result\orElse(static fn (string $e): Result\Ok => Result\ok(0));

        /** @var Result<int, string> $input */
        $input = Result\err('error');

        Assert::assertEquals(Result\ok(0), $fn($input));
    }

    #[Test]
    #[TestDox('orElse passes through Ok')]
    public function orElseOk(): void
    {
        $fn = Result\orElse(static fn (string $e): Result\Ok => Result\ok(0));

        /** @var Result<int, string> $input */
        $input = Result\ok(42);

        Assert::assertEquals(Result\ok(42), $fn($input));
    }

    #[Test]
    #[TestDox('inspect performs side-effect on Ok')]
    public function inspectOk(): void
    {
        $calls = [];
        $fn = Result\inspect(static function (int $x) use (&$calls): void {
            $calls[] = $x;
        });

        /** @var Result<int, string> $input */
        $input = Result\ok(42);

        $result = $fn($input);

        Assert::assertEquals(Result\ok(42), $result);
        Assert::assertSame([42], $calls);
    }

    #[Test]
    #[TestDox('inspect skips Err')]
    public function inspectErr(): void
    {
        $calls = [];
        $fn = Result\inspect(static function (mixed $x) use (&$calls): void {
            $calls[] = $x;
        });

        /** @var Result<int, string> $input */
        $input = Result\err('error');

        $result = $fn($input);

        Assert::assertEquals(Result\err('error'), $result);
        Assert::assertSame([], $calls);
    }

    #[Test]
    #[TestDox('inspectErr performs side-effect on Err')]
    public function inspectErrOnErr(): void
    {
        $calls = [];
        $fn = Result\inspectErr(static function (string $e) use (&$calls): void {
            $calls[] = $e;
        });

        /** @var Result<int, string> $input */
        $input = Result\err('error');

        $result = $fn($input);

        Assert::assertEquals(Result\err('error'), $result);
        Assert::assertSame(['error'], $calls);
    }

    #[Test]
    #[TestDox('inspectErr skips Ok')]
    public function inspectErrOnOk(): void
    {
        $calls = [];
        $fn = Result\inspectErr(static function (mixed $e) use (&$calls): void {
            $calls[] = $e;
        });

        /** @var Result<int, string> $input */
        $input = Result\ok(42);

        $result = $fn($input);

        Assert::assertEquals(Result\ok(42), $result);
        Assert::assertSame([], $calls);
    }

    #[Test]
    #[TestDox('unwrapOr returns Ok value')]
    public function unwrapOrOk(): void
    {
        $fn = Result\unwrapOr(0);

        /** @var Result<int, string> $input */
        $input = Result\ok(42);

        Assert::assertSame(42, $fn($input));
    }

    #[Test]
    #[TestDox('unwrapOr returns default on Err')]
    public function unwrapOrErr(): void
    {
        $fn = Result\unwrapOr(0);

        /** @var Result<int, string> $input */
        $input = Result\err('error');

        Assert::assertSame(0, $fn($input));
    }

    #[Test]
    #[TestDox('unwrapOrElse returns Ok value')]
    public function unwrapOrElseOk(): void
    {
        $fn = Result\unwrapOrElse(static fn (string $e): int => 0);

        /** @var Result<int, string> $input */
        $input = Result\ok(42);

        Assert::assertSame(42, $fn($input));
    }

    #[Test]
    #[TestDox('unwrapOrElse computes default on Err')]
    public function unwrapOrElseErr(): void
    {
        $fn = Result\unwrapOrElse(static fn (string $e): string => "recovered: {$e}");

        /** @var Result<int, string> $input */
        $input = Result\err('error');

        Assert::assertSame('recovered: error', $fn($input));
    }

    #[Test]
    #[TestDox('expect returns Ok value')]
    public function expectOk(): void
    {
        $fn = Result\expect('should not fail');

        /** @var Result<int, string> $input */
        $input = Result\ok(42);

        Assert::assertSame(42, $fn($input));
    }

    #[Test]
    #[TestDox('expect throws on Err')]
    public function expectErr(): void
    {
        $fn = Result\expect('expected value');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('expected value');

        /** @var Result<int, string> $input */
        $input = Result\err('error');
        $fn($input);
    }
}
