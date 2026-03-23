<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit\Result;

use EndouMame\PhpMonad\Result;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\Provider\ValueProvider;
use EndouMame\PhpMonad\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[TestDox('Result - InspectTest')]
#[CoversClass(Result::class)]
final class InspectTest extends TestCase
{
    use ValueProvider;

    #[Test]
    #[TestDox('inspectOk test')]
    #[DataProvider('values')]
    public function inspectOk(mixed $value): void
    {
        $result = Result\ok($value);

        ['result' => $result, 'calls' => $calls] = $this->inspect($result);

        Assert::assertSame($result, $result);
        Assert::assertSame([$value], $calls);
    }

    #[Test]
    #[TestDox('inspectNone test')]
    public function inspectNone(): void
    {
        $result = Result\err(null);

        ['result' => $result, 'calls' => $calls] = $this->inspect($result);

        Assert::assertSame($result, $result);
        Assert::assertSame([], $calls);
    }

    #[Test]
    #[TestDox('inspectErrOk test')]
    public function inspectErrOk(): void
    {
        $result = Result\ok(null);

        ['result' => $result, 'calls' => $calls] = $this->inspectErr($result);

        Assert::assertSame($result, $result);
        Assert::assertSame([], $calls);
    }

    #[Test]
    #[TestDox('inspectErrNone test')]
    #[DataProvider('values')]
    public function inspectErrNone(mixed $value): void
    {
        $result = Result\err($value);

        ['result' => $result, 'calls' => $calls] = $this->inspectErr($result);

        Assert::assertSame($result, $result);
        Assert::assertSame([$value], $calls);
    }

    /**
     * @template T
     * @template E
     * @param  Result<T, E>                                $result
     * @return array{result:Result<T, E>, calls: array<T>}
     */
    private function inspect(Result $result): array
    {
        $calls = [];

        $result = $result->inspect(static function (mixed $value) use (&$calls): void {
            $calls[] = $value;
        });

        return ['result' => $result, 'calls' => $calls];
    }

    /**
     * @template T
     * @template E
     * @param  Result<T, E>                                $result
     * @return array{result:Result<T, E>, calls: array<E>}
     */
    private function inspectErr(Result $result): array
    {
        $calls = [];

        $result = $result->inspectErr(static function (mixed $value) use (&$calls): void {
            $calls[] = $value;
        });

        return ['result' => $result, 'calls' => $calls];
    }
}
