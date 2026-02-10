<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Result;

use Closure;
use Throwable;
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;

/**
 * Return a `Result\Ok` Result containing `$value`.
 *
 * @template U
 * @param  U            $value
 * @return Result\Ok<U>
 */
function ok(mixed $value = true): Result\Ok
{
    return Result\Ok::unit($value);
}

/**
 * Return a `Result\Err` result.
 *
 * @template F
 * @param  F             $value
 * @return Result\Err<F>
 */
function err(mixed $value): Result\Err
{
    return Result\Err::unit($value);
}

/**
 * Creates a Result from a Closure that may throw an exception.
 *
 * @template T
 * @template E
 * @param  Closure(): T          $closure
 * @param  Closure(Throwable): E $errorHandler
 * @return Result<T, E>
 */
function fromThrowable(Closure $closure, Closure $errorHandler): Result
{
    try {
        return Result\ok($closure());
    } catch (Throwable $e) {
        return Result\err($errorHandler($e));
    }
}

/**
 * Converts from `Result<Result<T, E>, E>` to `Result<T, E>`.
 *
 * @template T
 * @template E
 * @template E1 of E
 * @template E2 of E
 * @param  Result<Result<T, E1>, E2> $result
 * @return Result<T, E>
 */
function flatten(Result $result): Result
{
    // @phpstan-ignore return.type
    return $result->mapOrElse(
        static fn (Result $r) => $r,
        static fn (mixed $err) => Result\err($err),
    );
}

/**
 * Transposes a `Result` of an `Option` into an `Option` of a `Result`.
 *
 * `Ok(None)` will be mapped to `None`.
 * `Ok(Some(_))` and `Err(_)` will be mapped to `Some(Ok(_))` and `Some(Err(_))`.
 *
 * @template U
 * @template F
 * @param  Result<Option<U>, F> $result
 * @return Option<Result<U, F>>
 */
function transpose(Result $result): Option
{
    // @phpstan-ignore return.type
    return $result->mapOrElse(
        static fn (Option $option) => $option->map(Result\ok(...)),
        static fn () => Option\some(clone $result),
    );
}

/**
 * @template T
 * @template E
 * @param  Result<covariant T, covariant E>                  ...$results
 * @return Result<bool, non-empty-list<E>>
 */
function combine(Result ...$results): Result
{
    $errs = array_filter($results, static fn (Result $result) => $result->isErr());
    if (count($errs) > 0) {
        return Result\err(array_values(array_map(static fn (Result $result) => $result->unwrapErr(), $errs)));
    }

    return Result\ok();
}
