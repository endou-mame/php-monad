<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Option;

use Closure;
use NoDiscard;
use Override;
use Throwable;
use Traversable;
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;

/**
 * @template T
 * @implements Option<T>
 */
final readonly class Some implements Option
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
     * @param  Closure(T): Option<U> $right
     * @return Option<U>
     */
    /**
     * @phpstan-ignore method.childParameterType
     */
    #[Override]
    public function andThen(Closure $right): Option
    {
        return $right($this->value);
    }

    #[Override]
    public function isSome(): true
    {
        return true;
    }

    #[Override]
    public function isNone(): false
    {
        return false;
    }

    #[Override]
    public function isSomeAnd(Closure $predicate): bool
    {
        return $predicate($this->value);
    }

    /**
     * @throws void
     */
    #[Override]
    public function expect(string $message): mixed
    {
        return $this->value;
    }

    /**
     * @throws void
     */
    #[NoDiscard]
    #[Override]
    public function unwrap(): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    #[NoDiscard]
    #[Override]
    public function unwrapOr(mixed $default): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    #[NoDiscard]
    #[Override]
    public function unwrapOrElse(Closure $default): mixed
    {
        return $this->value;
    }

    /**
     * @template E of \Throwable
     * @param  E     $exception
     * @return T
     * @throws never
     */
    #[NoDiscard]
    #[Override]
    public function unwrapOrThrow(Throwable $exception): mixed
    {
        return $this->value;
    }

    #[Override]
    public function inspect(Closure $callback): self
    {
        $callback($this->value);

        return $this;
    }

    #[Override]
    public function and(Option $right): Option
    {
        return $right;
    }

    /**
     * @return $this
     */
    #[Override]
    public function or(Option $right): Option
    {
        return $this;
    }

    /**
     * @return $this
     */
    #[Override]
    public function orElse(Closure $right): Option
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

    #[Override]
    public function xor(Option $right): Option
    {
        return $right instanceof Option\None
        ? $this
        : Option\none();
    }

    #[Override]
    public function filter(Closure $predicate): Option
    {
        return $predicate($this->value)
        ? $this
        : Option\none();
    }

    #[Override]
    public function map(Closure $callback): Option
    {
        return Option\some($callback($this->value));
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
     * @template E
     * @param  E            $err
     * @return Result\Ok<T>
     */
    #[Override]
    public function okOr(mixed $err): Result\Ok
    {
        return Result\ok($this->value);
    }

    /**
     * @template E
     * @param  Closure() :E $err
     * @return Result\Ok<T>
     */
    #[Override]
    public function okOrElse(Closure $err): Result\Ok
    {
        return Result\ok($this->value);
    }

    #[Override]
    public function getIterator(): Traversable
    {
        yield $this->value;
    }
}
