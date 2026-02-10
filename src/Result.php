<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad;

use Closure;
use Deprecated;
use JiriPudil\SealedClasses\Sealed;
use RuntimeException;
use Throwable;
use EndouMame\PhpMonad\Result\Err;
use EndouMame\PhpMonad\Result\Ok;

/**
 * Result monad as a `Either monad`.
 * Inspired by Rust's `Result` enum.
 * @see https://doc.rust-lang.org/std/result/enum.Result.html
 * @template T
 * @template E
 * @extends Monad<T>
 */
#[Sealed(permits: [Ok::class, Err::class])]
interface Result extends Monad
{
    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.is_ok
     * @phpstan-assert-if-true Ok<T> $this
     * @phpstan-assert-if-false Err<E> $this
     */
    public function isOk(): bool;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.is_err
     * @phpstan-assert-if-true Err<E> $this
     * @phpstan-assert-if-false Ok<T> $this
     */
    public function isErr(): bool;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.is_ok_and
     * @param Closure(T): bool $predicate
     */
    public function isOkAnd(Closure $predicate): bool;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.is_err_and
     * @param Closure(E): bool $predicate
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
     * @param  Closure(E): U $default
     * @return T|U
     */
    public function unwrapOrElse(Closure $default): mixed;

    /**
     * Returns the contained Ok value or throws the provided exception.
     *
     * @template F of \Throwable
     * @param  F $exception The exception to throw if the result is Err
     * @return T
     * @throws F
     */
    public function unwrapOrThrow(Throwable $exception): mixed;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.inspect
     * @param  Closure(T): mixed $callback
     * @return $this
     */
    public function inspect(Closure $callback): self;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.inspect_err
     * @param  Closure(E): mixed $callback
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
     * @param  Closure(T): Result<U, F>                                                                                  $right
     * @return (F is int|string|bool|null|float|array|iterable|callable|resource|object ? Result<U, E|F> : Result<U, E>)
     */
    public function andThen(Closure $right): self; /** @phpstan-ignore method.childParameterType */

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
     * @param  Closure(E): Result<T, F> $right
     * @return Result<T, F>
     */
    public function orElse(Closure $right): self;

    /**
     * Returns the contained Ok value or throws the provided exception.
     *
     * @template F of \Throwable
     * @param  F     $exception The exception to throw if the result is Err
     * @return $this
     * @throws F
     */
    public function orThrow(Throwable $exception): self;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.map
     * @template U
     * @param  Closure(T): U $callback
     * @return Result<U, E>
     */
    public function map(Closure $callback): self;

    /**
     * @see https://doc.rust-lang.org/std/result/enum.Result.html#method.map_err
     * @template F
     * @param  Closure(E): F $callback
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
     * @param  Closure(T): U $callback
     * @param  Closure(E): U $default
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

    /**
     * Applies one of two functions depending on whether the result is Ok or Err.
     * Comparable to neverthrow's `match` method.
     *
     * @template U
     * @template V
     * @param  Closure(T): U $okFn  Function to apply if the Result is Ok
     * @param  Closure(E): V $errFn Function to apply if the Result is Err
     * @return U|V           The result of applying the appropriate function
     */
    #[Deprecated]
    public function match(Closure $okFn, Closure $errFn): mixed;
}
