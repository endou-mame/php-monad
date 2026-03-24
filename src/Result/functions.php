<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Result;

use Closure;
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;
use Throwable;

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
 * @param  Result<Result<T, E>, E> $result
 * @return Result<T, E>
 */
function flatten(Result $result): Result
{
    if ($result->isErr()) {
        /** @var Result<T, E> $err */
        $err = $result;

        return $err;
    }

    /** @var Result<T, E> $inner */
    $inner = $result->unwrap();

    return $inner;
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
    if ($result->isErr()) {
        /** @var Option<Result<U, F>> $err */
        $err = Option\some(clone $result);

        return $err;
    }

    /** @var Option<U> $option */
    $option = $result->unwrap();

    if ($option->isNone()) {
        /** @var Option<Result<U, F>> $none */
        $none = Option\none();

        return $none;
    }

    /** @var Option<Result<U, F>> $ok */
    $ok = Option\some(Result\ok($option->unwrap()));

    return $ok;
}

/**
 * Applies a callback to the values of multiple `Result`s if all are `Ok`.
 *
 * Returns the first `Err` encountered, or `Ok` wrapping the callback's return value.
 *
 * @template E
 * @param  Result<covariant mixed, covariant E> ...$results
 * @return Result<mixed, E>
 */
function map_all(Closure $fn, Result ...$results): Result
{
    $values = [];
    foreach ($results as $result) {
        if ($result->isErr()) {
            return $result;
        }
        $values[] = $result->unwrap();
    }

    return ok($fn(...$values));
}

/**
 * Applies a `Result`-returning callback to the values of multiple `Result`s if all are `Ok`.
 *
 * Returns the first `Err` encountered, or the `Result` returned by the callback.
 *
 * @template E
 * @param  Result<covariant mixed, covariant E> ...$results
 * @return Result<mixed, E>
 */
function flat_map_all(Closure $fn, Result ...$results): Result
{
    $values = [];
    foreach ($results as $result) {
        if ($result->isErr()) {
            return $result;
        }
        $values[] = $result->unwrap();
    }

    return $fn(...$values);
}

/**
 * @template T
 * @template E
 * @param  Result<covariant T, covariant E> ...$results
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
