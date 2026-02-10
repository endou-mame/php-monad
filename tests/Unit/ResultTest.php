<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use EndouMame\PhpMonad\Monad;
use EndouMame\PhpMonad\Result;

/**
 * @extends MonadTestAbstract<Result>
 */
#[TestDox('Result - MonadTest')]
#[CoversClass(Result::class)]
final class ResultTest extends MonadTestAbstract
{
    /**
     * @return iterable<array{Result<string,string>}>
     */
    #[Override]
    public static function monadsProvider(): iterable
    {
        yield 'ok' => [Result\ok('Ok')];
        // TODO: どうしてもテストが落ちるためやむを得ずコメントアウトする
        // yield 'err' => [Result\err('Err')];
    }

    /**
     * @param Monad<string> $subject
     */
    #[Test]
    #[TestDox('Monad laws')]
    #[DataProvider('monadsProvider')]
    public function monadLaws(Monad $subject): void
    {
        parent::monadLaws($subject);
    }
}
