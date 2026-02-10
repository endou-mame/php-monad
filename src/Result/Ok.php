<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Result;

use Closure;
use Deprecated;
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
 * @template T
 * @implements Result<T, never>
 */
final readonly class Ok implements Result
{
    /**
     * @param T $value
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
     * @template U
     * @template F
     * @param  Closure(T) :Result<U, F> $right
     * @return Result<U, F>
     */
    /**
     * @phpstan-ignore method.childParameterType
     */
    #[Override]
    public function andThen(Closure $right): Result
    {
        // @phpstan-ignore return.type
        return $right($this->value);
    }

    #[Override]
    public function isOk(): true
    {
        return true;
    }

    #[Override]
    public function isErr(): false
    {
        return false;
    }

    #[Override]
    public function isOkAnd(Closure $predicate): bool
    {
        return $predicate($this->value);
    }

    #[Override]
    public function isErrAnd(Closure $predicate): false
    {
        return false;
    }

    /**
     * @return T
     */
    #[Override]
    public function expect(string $message): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    #[NoDiscard]
    #[Override]
    public function unwrap(): mixed
    {
        return $this->value;
    }

    /**
     * @throws RuntimeException
     */
    #[Override]
    public function unwrapErr(): never
    {
        throw new RuntimeException(sprintf('Unwrapping err on `Ok`: %s', serialize($this->value)));
    }

    #[NoDiscard]
    #[Override]
    public function unwrapOr(mixed $default): mixed
    {
        return $this->value;
    }

    #[NoDiscard]
    #[Override]
    public function unwrapOrElse(Closure $default): mixed
    {
        return $this->value;
    }

    /**
     * @template F of \Throwable
     * @param  F     $exception
     * @return T
     * @throws never
     */
    #[NoDiscard]
    #[Override]
    public function unwrapOrThrow(Throwable $exception): mixed
    {
        return $this->value;
    }

    /**
     * @return $this
     */
    #[Override]
    public function inspect(Closure $callback): self
    {
        $callback($this->value);

        return $this;
    }

    /**
     * @return $this
     */
    #[Override]
    public function inspectErr(Closure $callback): self
    {
        return $this;
    }

    #[Override]
    public function and(Result $right): Result
    {
        return $right;
    }

    /**
     * @return $this
     */
    #[Override]
    public function or(Result $right): self
    {
        return $this;
    }

    /**
     * @return $this
     */
    #[Override]
    public function orElse(Closure $right): self
    {
        return $this;
    }

    /**
     * @template F of \Throwable
     * @return $this
     * @throws never
     */
    #[Override]
    public function orThrow(Throwable $exception): self
    {
        return $this;
    }

    /**
     * @template U
     * @param  Closure(T) :U $callback
     * @return self<U>
     */
    #[Override]
    public function map(Closure $callback): self
    {
        return Result\ok($callback($this->value));
    }

    /**
     * @return $this
     */
    #[Override]
    public function mapErr(Closure $callback): self
    {
        return $this;
    }

    #[Override]
    public function mapOr(Closure $callback, mixed $default): mixed
    {
        return $callback($this->value);
    }

    #[Override]
    public function mapOrElse(Closure $callback, Closure $default): mixed
    {
        return $callback($this->value);
    }

    /**
     * @return Option\Some<T>
     */
    #[Override]
    public function ok(): Option\Some
    {
        return Option\some($this->value);
    }

    #[Override]
    public function err(): Option\None
    {
        return Option\none();
    }

    #[Override]
    public function getIterator(): Traversable
    {
        yield $this->value;
    }

    #[Deprecated]
    #[Override]
    public function match(Closure $okFn, Closure $errFn): mixed
    {
        return $okFn($this->value);
    }
}
