<?php

declare(strict_types=1);

namespace WizDevelop\PhpMonad;

use Closure;
use RuntimeException;
use Throwable;

/**
 * Result monad as a `Either monad`.
 * Inspired by Rust's `Result` enum.
 * @see https://doc.rust-lang.org/std/result/enum.Result.html
 * @template T
 * @template E
 * @extends Monad<T>
 */
interface Result extends Monad
{
    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.is_ok
     */
    public function isOk(): bool;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.is_err
     */
    public function isErr(): bool;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.is_ok_and
     * @param Closure(T) :bool $predicate
     */
    public function isOkAnd(Closure $predicate): bool;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.is_err_and
     * @param Closure(E) :bool $predicate
     */
    public function isErrAnd(Closure $predicate): bool;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.expect
     * @return T
     * @throws RuntimeException
     */
    public function expect(string $message): mixed;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.unwrap
     * @return T
     * @throws Throwable
     */
    public function unwrap(): mixed;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.unwrap_err
     * @return E
     * @throws RuntimeException
     */
    public function unwrapErr(): mixed;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.unwrap_or
     * @template U
     * @param  U   $default
     * @return T|U
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.unwrap_or_else
     * @template U
     * @param  Closure(E) :U $default
     * @return T|U
     */
    public function unwrapOrElse(Closure $default): mixed;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.inspect
     * @param  Closure(T) :mixed $callback
     * @return $this
     */
    public function inspect(Closure $callback): self;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.inspect_err
     * @param  Closure(E) :mixed $callback
     * @return $this
     */
    public function inspectErr(Closure $callback): self;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.and
     * @template U
     * @param  Result<U, E> $right
     * @return Result<U, E>
     */
    public function and(self $right): self;

    /**
     * NOTE: PHPdoc's completion by type specification in Closure doesn't work, so I'm redefining it.
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.and_then
     * @template U
     * @template F
     * @param  Closure(T) :Result<U, F> $right
     * @return Result<U, E|F>
     */
    public function andThen(Closure $right): self;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.or
     * @template F
     * @param  Result<T, F> $right
     * @return Result<T, F>
     */
    public function or(self $right): self;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.or_else
     * @template F
     * @param  Closure(E) :Result<T, F> $right
     * @return Result<T, F>
     */
    public function orElse(Closure $right): self;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.map
     * @template U
     * @param  Closure(T) :U $callback
     * @return Result<U, E>
     */
    public function map(Closure $callback): self;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.map_err
     * @template F
     * @param  Closure(E) :F $callback
     * @return Result<T, F>
     */
    public function mapErr(Closure $callback): self;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.map_or
     * @template U
     * @param  Closure(T):U $callback
     * @param  U            $default
     * @return U
     */
    public function mapOr(Closure $callback, mixed $default): mixed;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.map_or_else
     * @template U
     * @param  Closure(T):U $callback
     * @param  Closure(E):U $default
     * @return U
     */
    public function mapOrElse(Closure $callback, Closure $default): mixed;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.ok
     * @return Option<T>
     */
    public function ok(): Option;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.err
     * @return Option<E>
     */
    public function err(): Option;
}
