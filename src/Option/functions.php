<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Option;

use Closure;
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;
use Exception;
use Throwable;

use function is_a;

/**
 * Return a `Option\Some` option containing `$value`.
 *
 * @template T
 * @param  T              $value
 * @return Option\Some<T>
 */
function some(mixed $value): Option\Some
{
    return Option\Some::unit($value);
}

/**
 * Return a `Option\None` option containing no values.
 */
function none(): Option\None
{
    return Option\None::unit(null);
}

/**
 * Transform a value into an `Option`.
 * It will be a `Some` option containing `$value` if `$value` is different from `$noneValue` (default `null`)
 *
 * @template U
 * @template NoneValue
 *
 * @param U              $value
 * @param NoneValue|null $noneValue
 *
 * @return ($noneValue is null ? Option<U> : Option<U|NoneValue>)
 */
function from_value($value, mixed $noneValue = null): Option
{
    return $value === $noneValue
        ? Option\none()
        : Option\some($value);
}

/**
 * Execute a callable and transform the result into an `Option`.
 * It will be a `Some` option containing the result if it is different from `$noneValue` (default `null`).
 *
 * @template U
 * @template NoneValue
 *
 * @param callable():U   $callback
 * @param NoneValue|null $noneValue
 *
 * @return ($noneValue is null ? Option<U> : Option<U|NoneValue>)
 */
function of(callable $callback, mixed $noneValue = null): Option
{
    return Option\from_value($callback(), $noneValue);
}

/**
 * Execute a callable and transform the result into an `Option` as `Option\of()` does
 * but also return `Option\None` if it an exception matching $exceptionClass was thrown.
 *
 * @template U
 * @template NoneValue
 * @template E of \Throwable
 *
 * @param callable():U    $callback
 * @param NoneValue|null  $noneValue
 * @param class-string<E> $exceptionClass
 *
 * @return ($noneValue is null ? Option<U> : Option<U|NoneValue>)
 *
 * @throws Throwable
 */
function try_of(
    callable $callback,
    mixed $noneValue = null,
    string $exceptionClass = Exception::class,
): Option {
    try {
        return Option\of($callback, $noneValue);
    } catch (Throwable $th) {
        if (is_a($th, $exceptionClass)) {
            return Option\none();
        }

        throw $th;
    }
}

/**
 * Converts from `Option<Option<T>>` to `Option<T>`.
 *
 * @template U
 * @param Option<Option<U>> $option
 *
 * @return Option<U>
 */
function flatten(Option $option): Option
{
    return $option instanceof Option\None
        ? $option
        : $option->unwrap();
}

/**
 * Apply a function that returns Result if Some, or ok(null) if None.
 *
 * @template T
 * @template U
 * @template E
 *
 * @param Option<T>                  $option
 * @param (Closure(T): Result<U, E>) $fn
 *
 * @return Result<U|null, E>
 */
function traverse(Option $option, Closure $fn): Result
{
    if ($option->isNone()) {
        /** @var Result<U|null, E> $result */
        return Result\ok(null);
    }

    /** @var Result<U|null, E> $result */
    return $fn($option->unwrap());
}

/**
 * Transposes an `Option` of a `Result` into a `Result` of an `Option`.
 *
 * `None` will be mapped to `Ok(None)`.
 * `Some(Ok(_))` and `Some(Err(_))` will be mapped to `Ok(Some(_))` and `Err(_)`.
 *
 * @template U
 * @template E
 *
 * @param Option<Result<U, E>> $option
 *
 * @return Result<Option<U>, E>
 */
function transpose(Option $option): Result
{
    if ($option->isNone()) {
        /** @var Result<Option<U>, E> $none */
        return Result\ok(Option\none());
    }

    /** @var Result<U, E> $inner */
    $inner = $option->unwrap();

    if ($inner->isErr()) {
        /** @var Result<Option<U>, E> $err */
        return $inner;
    }

    /** @var Result<Option<U>, E> $ok */
    return Result\ok(Option\some($inner->unwrap()));
}
