<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit;

use EndouMame\PhpMonad\Monad;
use EndouMame\PhpMonad\Option;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

/**
 * @extends MonadTestAbstract<Option>
 */
#[TestDox('Option - MonadTest')]
#[CoversClass(Option::class)]
final class OptionTest extends MonadTestAbstract
{
    /**
     * @param Monad<string> $subject
     */
    #[Test]
    #[TestDox('Monad laws')]
    #[DataProvider('provideMonadLawsCases')]
    public function monadLaws(Monad $subject): void
    {
        parent::monadLaws($subject);
    }

    /**
     * @return iterable<array{Option<string>}>
     */
    #[Override]
    public static function provideMonadLawsCases(): iterable
    {
        yield 'just' => [Option\some('Monad')];
        yield 'nothing' => [Option\none()];
    }
}
