<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Option;

use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use RuntimeException;

#[TestDox('Option - Pipeline Functions')]
#[CoversFunction('EndouMame\PhpMonad\Option\map')]
#[CoversFunction('EndouMame\PhpMonad\Option\and_then')]
#[CoversFunction('EndouMame\PhpMonad\Option\or_else')]
#[CoversFunction('EndouMame\PhpMonad\Option\filter')]
#[CoversFunction('EndouMame\PhpMonad\Option\inspect')]
#[CoversFunction('EndouMame\PhpMonad\Option\unwrap_or')]
#[CoversFunction('EndouMame\PhpMonad\Option\unwrap_or_else')]
#[CoversFunction('EndouMame\PhpMonad\Option\expect')]
#[CoversFunction('EndouMame\PhpMonad\Option\ok_or')]
#[CoversFunction('EndouMame\PhpMonad\Option\ok_or_else')]
final class PipelineTest extends TestCase
{
    #[Test]
    #[TestDox('map transforms Some value')]
    public function mapSome(): void
    {
        $fn = Option\map(static fn (int $x): int => $x * 2);

        /** @var Option<int> $input */
        $input = Option\some(42);

        Assert::assertEquals(Option\some(84), $fn($input));
    }

    #[Test]
    #[TestDox('map passes through None')]
    public function mapNone(): void
    {
        $fn = Option\map(static fn (int $x): int => $x * 2);

        /** @var Option<int> $input */
        $input = Option\none();

        Assert::assertEquals(Option\none(), $fn($input));
    }

    #[Test]
    #[TestDox('andThen chains on Some')]
    public function andThenSome(): void
    {
        $fn = Option\and_then(static fn (int $x): Option\Some => Option\some($x + 1));

        /** @var Option<int> $input */
        $input = Option\some(42);

        Assert::assertEquals(Option\some(43), $fn($input));
    }

    #[Test]
    #[TestDox('andThen passes through None')]
    public function andThenNone(): void
    {
        $fn = Option\and_then(static fn (int $x): Option\Some => Option\some($x + 1));

        /** @var Option<int> $input */
        $input = Option\none();

        Assert::assertEquals(Option\none(), $fn($input));
    }

    #[Test]
    #[TestDox('orElse provides alternative on None')]
    public function orElseNone(): void
    {
        $fn = Option\or_else(static fn (): Option\Some => Option\some(0));

        /** @var Option<int> $input */
        $input = Option\none();

        Assert::assertEquals(Option\some(0), $fn($input));
    }

    #[Test]
    #[TestDox('orElse passes through Some')]
    public function orElseSome(): void
    {
        $fn = Option\or_else(static fn (): Option\Some => Option\some(0));

        /** @var Option<int> $input */
        $input = Option\some(42);

        Assert::assertEquals(Option\some(42), $fn($input));
    }

    #[Test]
    #[TestDox('filter keeps matching Some')]
    public function filterKeeps(): void
    {
        $fn = Option\filter(static fn (int $x): bool => $x > 10);

        /** @var Option<int> $input */
        $input = Option\some(42);

        Assert::assertEquals(Option\some(42), $fn($input));
    }

    #[Test]
    #[TestDox('filter rejects non-matching Some')]
    public function filterRejects(): void
    {
        $fn = Option\filter(static fn (int $x): bool => $x > 100);

        /** @var Option<int> $input */
        $input = Option\some(42);

        Assert::assertEquals(Option\none(), $fn($input));
    }

    #[Test]
    #[TestDox('filter passes through None')]
    public function filterNone(): void
    {
        $fn = Option\filter(static fn (int $x): bool => $x > 0);

        /** @var Option<int> $input */
        $input = Option\none();

        Assert::assertEquals(Option\none(), $fn($input));
    }

    #[Test]
    #[TestDox('inspect performs side-effect on Some')]
    public function inspectSome(): void
    {
        $calls = [];
        $fn = Option\inspect(static function (int $x) use (&$calls): void {
            $calls[] = $x;
        });

        /** @var Option<int> $input */
        $input = Option\some(42);

        $result = $fn($input);

        Assert::assertEquals(Option\some(42), $result);
        Assert::assertSame([42], $calls);
    }

    #[Test]
    #[TestDox('inspect skips None')]
    public function inspectNone(): void
    {
        $calls = [];
        $fn = Option\inspect(static function (mixed $x) use (&$calls): void {
            $calls[] = $x;
        });

        /** @var Option<int> $input */
        $input = Option\none();

        $result = $fn($input);

        Assert::assertEquals(Option\none(), $result);
        Assert::assertSame([], $calls);
    }

    #[Test]
    #[TestDox('unwrapOr returns Some value')]
    public function unwrapOrSome(): void
    {
        $fn = Option\unwrap_or(0);

        /** @var Option<int> $input */
        $input = Option\some(42);

        Assert::assertSame(42, $fn($input));
    }

    #[Test]
    #[TestDox('unwrapOr returns default on None')]
    public function unwrapOrNone(): void
    {
        $fn = Option\unwrap_or(0);

        /** @var Option<int> $input */
        $input = Option\none();

        Assert::assertSame(0, $fn($input));
    }

    #[Test]
    #[TestDox('unwrapOrElse returns Some value')]
    public function unwrapOrElseSome(): void
    {
        $fn = Option\unwrap_or_else(static fn (): int => 0);

        /** @var Option<int> $input */
        $input = Option\some(42);

        Assert::assertSame(42, $fn($input));
    }

    #[Test]
    #[TestDox('unwrapOrElse computes default on None')]
    public function unwrapOrElseNone(): void
    {
        $fn = Option\unwrap_or_else(static fn (): int => 99);

        /** @var Option<int> $input */
        $input = Option\none();

        Assert::assertSame(99, $fn($input));
    }

    #[Test]
    #[TestDox('expect returns Some value')]
    public function expectSome(): void
    {
        $fn = Option\expect('should not fail');

        /** @var Option<int> $input */
        $input = Option\some(42);

        Assert::assertSame(42, $fn($input));
    }

    #[Test]
    #[TestDox('expect throws on None')]
    public function expectNone(): void
    {
        $fn = Option\expect('expected value');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('expected value');

        /** @var Option<int> $input */
        $input = Option\none();
        $fn($input);
    }

    #[Test]
    #[TestDox('okOr converts Some to Ok')]
    public function okOrSome(): void
    {
        $fn = Option\ok_or('not found');

        /** @var Option<int> $input */
        $input = Option\some(42);

        Assert::assertEquals(Result\ok(42), $fn($input));
    }

    #[Test]
    #[TestDox('okOr converts None to Err')]
    public function okOrNone(): void
    {
        $fn = Option\ok_or('not found');

        /** @var Option<int> $input */
        $input = Option\none();

        Assert::assertEquals(Result\err('not found'), $fn($input));
    }

    #[Test]
    #[TestDox('okOrElse converts Some to Ok')]
    public function okOrElseSome(): void
    {
        $fn = Option\ok_or_else(static fn (): string => 'not found');

        /** @var Option<int> $input */
        $input = Option\some(42);

        Assert::assertEquals(Result\ok(42), $fn($input));
    }

    #[Test]
    #[TestDox('okOrElse converts None to Err with lazy value')]
    public function okOrElseNone(): void
    {
        $fn = Option\ok_or_else(static fn (): string => 'not found');

        /** @var Option<int> $input */
        $input = Option\none();

        Assert::assertEquals(Result\err('not found'), $fn($input));
    }
}
