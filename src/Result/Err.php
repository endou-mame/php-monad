<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Result;

use Closure;
use Deprecated;
use EmptyIterator;
use NoDiscard;
use Override;
use RuntimeException;
use Throwable;
use Traversable;
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;

use function serialize;
use function sprintf;

/**
 * @template E
 * @implements Result<never, E>
 */
final readonly class Err implements Result
{
    /**
     * @param E $value
     */
    private function __construct(private mixed $value)
    {
    }

    /**
     * @internal
     * @template TValue
     * @param  TValue       $value
     * @return self<TValue>
     */
    #[Override]
    public static function unit(mixed $value): self
    {
        return new self($value);
    }

    /**
     * @phpstan-ignore method.childParameterType, missingType.generics
     */
    #[Override]
    public function andThen(Closure $right): self
    {
        return $this;
    }

    #[Override]
    public function isOk(): false
    {
        return false;
    }

    #[Override]
    public function isErr(): true
    {
        return true;
    }

    #[Override]
    public function isOkAnd(Closure $predicate): false
    {
        return false;
    }

    #[Override]
    public function isErrAnd(Closure $predicate): bool
    {
        return $predicate($this->value);
    }

    /**
     * @throws RuntimeException
     */
    #[Override]
    public function expect(string $message): never
    {
        if ($this->value instanceof Throwable) {
            throw new RuntimeException($message, previous: $this->value);
        }

        throw new RuntimeException(sprintf($message, serialize($this->value)));
    }

    /**
     * @throws Throwable
     */
    #[Override]
    public function unwrap(): never
    {
        if ($this->value instanceof Throwable) {
            throw $this->value;
        }

        $this->expect('Unwrapping `Err`: %s');
    }

    /**
     * @return E
     */
    #[NoDiscard]
    #[Override]
    public function unwrapErr(): mixed
    {
        return $this->value;
    }

    /**
     * @template U
     * @param  U $default
     * @return U
     */
    #[NoDiscard]
    #[Override]
    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    /**
     * @template U
     * @param  Closure(E): U $default
     * @return U
     */
    #[NoDiscard]
    #[Override]
    public function unwrapOrElse(Closure $default): mixed
    {
        return $default($this->value);
    }

    /**
     * @template F of \Throwable
     * @param  F $exception
     * @throws F
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
    public function inspectErr(Closure $callback): self
    {
        $callback($this->value);

        return $this;
    }

    /**
     * @return $this
     */
    #[Override]
    public function and(Result $right): self
    {
        return $this;
    }

    #[Override]
    public function or(Result $right): Result
    {
        return $right;
    }

    #[Override]
    public function orElse(Closure $right): Result
    {
        return $right($this->value);
    }

    /**
     * @template F of \Throwable
     * @throws F
     */
    #[Override]
    public function orThrow(Throwable $exception): never
    {
        throw $exception;
    }

    /**
     * @return $this
     */
    #[Override]
    public function map(Closure $callback): self
    {
        return $this;
    }

    /**
     * @template F
     * @param  Closure(E): F $callback
     * @return self<F>
     */
    #[Override]
    public function mapErr(Closure $callback): self
    {
        return Result\err($callback($this->value));
    }

    #[Override]
    public function mapOr(Closure $callback, mixed $default): mixed
    {
        return $default;
    }

    #[Override]
    public function ok(): Option\None
    {
        return Option\none();
    }

    /**
     * @return Option\Some<E>
     */
    #[Override]
    public function err(): Option\Some
    {
        return Option\some($this->value);
    }

    #[Override]
    public function mapOrElse(Closure $callback, Closure $default): mixed
    {
        return $default($this->value);
    }

    #[Override]
    public function getIterator(): Traversable
    {
        return new EmptyIterator();
    }

    #[Deprecated]
    #[Override]
    public function match(Closure $okFn, Closure $errFn): mixed
    {
        return $errFn($this->value);
    }
}
