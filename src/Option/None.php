<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Option;

use Closure;
use EmptyIterator;
use NoDiscard;
use Override;
use RuntimeException;
use Throwable;
use Traversable;
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;

/**
 * @implements Option<never>
 */
enum None implements Option
{
    case instance;

    /**
     * @internal
     * @return self<never>
     */
    #[Override]
    public static function unit(mixed $value): self
    {
        return self::instance;
    }

    /**
     * @return $this
     */
    /**
     * @phpstan-ignore method.childParameterType
     */
    #[Override]
    public function andThen(Closure $right): self
    {
        return $this;
    }

    #[Override]
    public function isSome(): false
    {
        return false;
    }

    #[Override]
    public function isNone(): true
    {
        return true;
    }

    #[Override]
    public function isSomeAnd(Closure $predicate): false
    {
        return false;
    }

    /**
     * @throws RuntimeException
     */
    #[Override]
    public function expect(string $message): never
    {
        throw new RuntimeException($message);
    }

    /**
     * @throws RuntimeException
     */
    #[Override]
    public function unwrap(): never
    {
        $this->expect('Unwrapping a `None` value');
    }

    #[NoDiscard]
    #[Override]
    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    #[NoDiscard]
    #[Override]
    public function unwrapOrElse(Closure $default): mixed
    {
        return $default();
    }

    /**
     * @template E of \Throwable
     * @param  E $exception
     * @throws E
     */
    #[Override]
    public function unwrapOrThrow(Throwable $exception): never
    {
        throw $exception;
    }

    /**
     * @return $this
     */
    #[Override]
    public function inspect(Closure $callback): self
    {
        return $this;
    }

    /**
     * @return $this
     */
    #[Override]
    public function and(Option $right): Option
    {
        return $this;
    }

    #[Override]
    public function or(Option $right): Option
    {
        return $right;
    }

    #[Override]
    public function orElse(Closure $right): Option
    {
        return $right();
    }

    /**
     * @template F of \Throwable
     * @param  F $exception
     * @throws F
     */
    #[Override]
    public function orThrow(Throwable $exception): never
    {
        throw $exception;
    }

    #[Override]
    public function xor(Option $right): Option
    {
        return $right;
    }

    #[Override]
    public function filter(Closure $predicate): Option
    {
        return $this;
    }

    #[Override]
    public function map(Closure $callback): Option
    {
        return $this;
    }

    #[Override]
    public function mapOr(Closure $callback, mixed $default): mixed
    {
        return $default;
    }

    #[Override]
    public function mapOrElse(Closure $callback, Closure $default): mixed
    {
        return $default();
    }

    /**
     * @template E
     * @param  E             $err
     * @return Result\Err<E>
     */
    #[Override]
    public function okOr(mixed $err): Result\Err
    {
        return Result\err($err);
    }

    /**
     * @template E
     * @param  Closure() :E  $err
     * @return Result\Err<E>
     */
    #[Override]
    public function okOrElse(Closure $err): Result\Err
    {
        return Result\err($err());
    }

    #[Override]
    public function getIterator(): Traversable
    {
        return new EmptyIterator();
    }
}
