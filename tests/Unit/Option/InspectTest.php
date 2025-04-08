<?php

declare(strict_types=1);

namespace WizDevelop\PhpMonad\Tests\Unit\Option;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use WizDevelop\PhpMonad\Option;
use WizDevelop\PhpMonad\Tests\Assert;
use WizDevelop\PhpMonad\Tests\Provider\ValueProvider;
use WizDevelop\PhpMonad\Tests\TestCase;

#[TestDox('Option - InspectTest')]
#[CoversClass(Option::class)]
final class InspectTest extends TestCase
{
    use ValueProvider;

    #[Test]
    #[TestDox('inspectSome test')]
    #[DataProvider('values')]
    public function inspectSome(mixed $value): void
    {
        $option = Option\some($value);

        ['result' => $result, 'calls' => $calls] = $this->inspect($option);

        Assert::assertSame($option, $result);
        Assert::assertSame([$value], $calls);
    }

    #[Test]
    #[TestDox('inspectNone test')]
    public function inspectNone(): void
    {
        $option = Option\none();

        ['result' => $result, 'calls' => $calls] = $this->inspect($option);

        Assert::assertSame($option, $result);
        Assert::assertSame([], $calls);
    }

    /**
     * @template T
     * @param  Option<T>                                $option
     * @return array{result:Option<T>, calls: array<T>}
     */
    private function inspect(Option $option): array
    {
        $calls = [];

        $option = $option->inspect(static function (mixed $value) use (&$calls): void {
            $calls[] = $value;
        });

        return ['result' => $option, 'calls' => $calls];
    }
}
