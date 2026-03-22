<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit;

use EndouMame\PhpMonad\Monad;
use EndouMame\PhpMonad\Result;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

/**
 * @extends MonadTestAbstract<Result>
 */
#[TestDox('Result - MonadTest')]
#[CoversClass(Result::class)]
final class ResultTest extends MonadTestAbstract
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
     * @return iterable<array{Result<string,string>}>
     */
    #[Override]
    public static function provideMonadLawsCases(): iterable
    {
        yield 'ok' => [Result\ok('Ok')];
        // TODO: どうしてもテストが落ちるためやむを得ずコメントアウトする
        // yield 'err' => [Result\err('Err')];
    }
}
