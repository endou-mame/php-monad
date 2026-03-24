<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Result;

use Closure;
use EndouMame\PhpMonad\Result;

/**
 * Pipeline function: Maps the Ok value using the callback.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\map(fn($x) => $x * 2)
 *
 * @template T
 * @template U
 * @param  Closure(T): U                               $callback
 * @return Closure(Result<T, mixed>): Result<U, mixed>
 */
function map(Closure $callback): Closure
{
    return static fn (Result $result): Result => $result->map($callback);
}

/**
 * Pipeline function: Maps the Err value using the callback.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\map_err(fn($e) => "Error: {$e}")
 *
 * @template E
 * @template F
 * @param  Closure(E): F                               $callback
 * @return Closure(Result<mixed, E>): Result<mixed, F>
 */
function map_err(Closure $callback): Closure
{
    return static fn (Result $result): Result => $result->mapErr($callback);
}

/**
 * Pipeline function: Chains a Result-returning operation on Ok value.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\and_then(fn($x) => validate($x))
 *
 * @template T
 * @template U
 * @template F
 * @param  Closure(T): Result<U, F>                    $callback
 * @return Closure(Result<T, mixed>): Result<U, mixed>
 */
function and_then(Closure $callback): Closure
{
    return static fn (Result $result): Result => $result->andThen($callback);
}

/**
 * Pipeline function: Handles Err by calling a Result-returning function.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\or_else(fn($e) => recover($e))
 *
 * @template E
 * @template F
 * @param  Closure(E): Result<mixed, F>                $callback
 * @return Closure(Result<mixed, E>): Result<mixed, F>
 */
function or_else(Closure $callback): Closure
{
    return static fn (Result $result): Result => $result->orElse($callback);
}

/**
 * Pipeline function: Performs a side-effect on Ok value, passing through the Result.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\inspect(fn($x) => logger()->info("Got: {$x}"))
 *
 * @template T
 * @param  Closure(T): mixed                           $callback
 * @return Closure(Result<T, mixed>): Result<T, mixed>
 */
function inspect(Closure $callback): Closure
{
    return static fn (Result $result): Result => $result->inspect($callback);
}

/**
 * Pipeline function: Performs a side-effect on Err value, passing through the Result.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\inspect_err(fn($e) => logger()->error($e))
 *
 * @template E
 * @param  Closure(E): mixed                           $callback
 * @return Closure(Result<mixed, E>): Result<mixed, E>
 */
function inspect_err(Closure $callback): Closure
{
    return static fn (Result $result): Result => $result->inspectErr($callback);
}

/**
 * Pipeline function: Unwraps the Ok value or returns the default.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\unwrap_or(0)
 *
 * @template U
 * @param  U                                    $default
 * @return Closure(Result<mixed, mixed>): mixed
 */
function unwrap_or(mixed $default): Closure
{
    return static fn (Result $result): mixed => $result->unwrapOr($default);
}

/**
 * Pipeline function: Unwraps the Ok value or computes a default from the Err.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\unwrap_or_else(fn($e) => fallback($e))
 *
 * @template E
 * @template U
 * @param  Closure(E): U                    $callback
 * @return Closure(Result<mixed, E>): mixed
 */
function unwrap_or_else(Closure $callback): Closure
{
    return static fn (Result $result): mixed => $result->unwrapOrElse($callback);
}

/**
 * Pipeline function: Unwraps the Ok value or throws RuntimeException with the message.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\expect('Value must be present')
 *
 * @return Closure(Result<mixed, mixed>): mixed
 */
function expect(string $message): Closure
{
    return static fn (Result $result): mixed => $result->expect($message);
}
