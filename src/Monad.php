<?php

declare(strict_types=1);

namespace WizDevelop\PhpMonad;

use Closure;
use IteratorAggregate;

/**
 * Monad interface.
 * @template T
 * @extends IteratorAggregate<T>
 * @immutable
 */
interface Monad extends IteratorAggregate
{
    /**
     * `return` in Haskell. (`Unit` operation.)
     * @template TValue
     * @param  TValue        $value
     * @return Monad<TValue>
     */
    public static function unit(mixed $value): self;

    /**
     * `>>=` in Haskell. (`Bind` operation.)
     * @template U
     * @param  Closure(T): Monad<U> $right
     * @return Monad<U>
     */
    public function andThen(Closure $right): self;
}
