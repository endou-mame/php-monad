<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad;

use Closure;
use JiriPudil\SealedClasses\Sealed;
use RuntimeException;
use Throwable;
use EndouMame\PhpMonad\Option\None;
use EndouMame\PhpMonad\Option\Some;

/**
 * Option monad as a `Maybe monad`.
 * Inspired by Rust's `Option` enum.
 * @see https://doc.rust-lang.org/std/option/enum.Option.html
 * @template T
 * @extends Monad<T>
 */
#[Sealed(permits: [Some::class, None::class])]
interface Option extends Monad
{
    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.is_some
     * @phpstan-assert-if-true Some<T> $this
     * @phpstan-assert-if-false None $this
     */
    public function isSome(): bool;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.is_none
     * @phpstan-assert-if-true None $this
     * @phpstan-assert-if-false Some<T> $this
     */
    public function isNone(): bool;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.is_some_and
     *
     * @param Closure(T): bool $predicate
     */
    public function isSomeAnd(Closure $predicate): bool;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.expect
     *
     * @return T
     * @throws RuntimeException
     */
    public function expect(string $message): mixed;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.unwrap
     *
     * @return T
     * @throws RuntimeException
     */
    public function unwrap(): mixed;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.unwrap_or
     *
     * @template U
     * @param  U   $default
     * @return T|U
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.unwrap_or_else
     *
     * @template U
     * @param  Closure(): U $default
     * @return T|U
     */
    public function unwrapOrElse(Closure $default): mixed;

    /**
     * Returns the contained Some value or throws the provided exception.
     *
     * @template E of \Throwable
     * @param  E $exception The exception to throw if the option is None
     * @return T
     * @throws E
     */
    public function unwrapOrThrow(Throwable $exception): mixed;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.inspect
     *
     * @param  Closure(T): mixed $callback
     * @return $this
     */
    public function inspect(Closure $callback): self;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.and
     *
     * @template U
     * @param  Option<U> $right
     * @return Option<U>
     */
    public function and(self $right): self;

    /**
     * NOTE: PHPdoc's completion by type specification in Closure doesn't work, so I'm redefining it.
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.and_then
     *
     * @template U
     * @param  Closure(T): Option<U> $right
     * @return Option<U>
     */
    public function andThen(Closure $right): self; /** @phpstan-ignore method.childParameterType */

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.or
     *
     * @template U
     * @param  Option<U>   $right
     * @return Option<T|U>
     */
    public function or(self $right): self;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.or_else
     *
     * @template U
     * @param  Closure(): Option<U> $right
     * @return Option<T|U>
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
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.xor
     *
     * @template U
     * @param  Option<U>   $right
     * @return Option<T|U>
     */
    public function xor(self $right): self;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.filter
     *
     * @param  Closure(T): bool $predicate
     * @return Option<T>
     */
    public function filter(Closure $predicate): self;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.map
     *
     * @template U
     * @param  Closure(T): U $callback
     * @return Option<U>
     */
    public function map(Closure $callback): self;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.map_or
     *
     * @template U
     * @template V
     * @param  Closure(T): U $callback
     * @param  V             $default
     * @return U|V
     */
    public function mapOr(Closure $callback, mixed $default): mixed;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.map_or_else
     *
     * @template U
     * @template V
     * @param  Closure(T): U $callback
     * @param  Closure(): V  $default
     * @return U|V
     */
    public function mapOrElse(Closure $callback, Closure $default): mixed;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.ok_or
     * @template E
     * @param  E            $err
     * @return Result<T, E>
     */
    public function okOr(mixed $err): Result;

    /**
     * @see https://doc.rust-lang.org/std/option/enum.Option.html#method.ok_or_else
     *
     * @template E
     * @param  Closure(): E $err
     * @return Result<T, E>
     */
    public function okOrElse(Closure $err): Result;
}
