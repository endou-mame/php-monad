<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Unit;

use BadFunctionCallException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use ReflectionClass;
use ReflectionFunction;
use ReflectionNamedType;
use EndouMame\PhpMonad\Monad;
use EndouMame\PhpMonad\Tests\Assert;
use EndouMame\PhpMonad\Tests\TestCase;

use function debug_backtrace;
use function is_a;

use const DEBUG_BACKTRACE_IGNORE_ARGS;

/**
 * @template TMonad of Monad
 */
abstract class MonadTestAbstract extends TestCase
{
    /**
     * @return iterable<array{TMonad<string>}>
     */
    abstract public static function monadsProvider(): iterable;

    /**
     * Test Monad laws
     *
     * @param TMonad<string> $subject
     * @see https://wiki.haskell.org/Monad_laws
     */
    #[Test]
    #[TestDox('Monad laws')]
    #[DataProvider('monadsProvider')]
    public function monadLaws(Monad $subject): void
    {
        $monad = $subject::class;

        $return = fn (string $v) => $this->_return($v, $monad);

        $f = fn (string $n) => $this->_return($n . 1, $monad);
        $g = fn (string $n) => $this->_return($n . 2, $monad);

        Assert::assertEquals(
            $monad::unit('x')->andThen($f),
            $f('x'),
            'Monad law - Left identity (return x >>= f == f x)'
        );
        Assert::assertEquals(
            $subject->andThen($return),
            $subject,
            'Monad law - Right identity (m >>= return == m)'
        );
        Assert::assertEquals(
            $subject->andThen($f)->andThen($g),
            $subject->andThen(static fn ($x) => $f($x)->andThen($g)),
            'Monad law - Associativity (m >>= f) >>= g == m >>= (\x -> f x >>= g)'
        );
    }

    /**
     * @template T
     * @template M of Monad
     * @param  T               $v
     * @param  class-string<M> $monad
     * @return M<T>
     */
    private function _return($v, ?string $monad = null): Monad
    {
        if ($monad === null) {
            $ref_type = self::get_caller_type(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]);
            if ($ref_type === null) {
                throw new BadFunctionCallException('Please call me from statically typed method');
            }

            $monad = $ref_type->getName();
        }

        if (!is_a($monad, Monad::class, true)) {
            throw new BadFunctionCallException('Class name $monad must be implements Monad');
        }

        return $monad::unit($v);
    }

    /**
     * @param array{class?:class-string,function?:string} $backtrace
     */
    private function get_caller_type(array $backtrace): ?ReflectionNamedType
    {
        $ref_type = null;
        if (isset($backtrace['class'], $backtrace['function'])) {
            $ref_class = new ReflectionClass($backtrace['class']);
            $ref_method = $ref_class->getMethod($backtrace['function']);
            $ref_type = $ref_method->getReturnType();
        } elseif ($function = ($backtrace['function'] ?? null)) {/** @phpstan-ignore elseif.condNotBoolean */
            $ref_function = new ReflectionFunction($function);
            $ref_type = $ref_function->getReturnType();
        }

        if ($ref_type === null) {
            return null;
        }

        assert($ref_type instanceof ReflectionNamedType);

        return $ref_type;
    }
}
