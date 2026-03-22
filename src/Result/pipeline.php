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
 * @template U
 * @param  Closure(mixed): U                               $callback
 * @return Closure(Result<mixed, mixed>): Result<U, mixed>
 */
function map(Closure $callback): Closure
{
    // @var Closure(Result<mixed, mixed>): Result<U, mixed>
    return static fn (Result $result): Result => $result->map($callback);
}

/**
 * Pipeline function: Maps the Err value using the callback.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\mapErr(fn($e) => "Error: {$e}")
 *
 * @template F
 * @param  Closure(mixed): F                               $callback
 * @return Closure(Result<mixed, mixed>): Result<mixed, F>
 */
function mapErr(Closure $callback): Closure
{
    // @var Closure(Result<mixed, mixed>): Result<mixed, F>
    return static fn (Result $result): Result => $result->mapErr($callback);
}

/**
 * Pipeline function: Chains a Result-returning operation on Ok value.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\andThen(fn($x) => validate($x))
 *
 * @template U
 * @template F
 * @param  Closure(mixed): Result<U, F>                    $callback
 * @return Closure(Result<mixed, mixed>): Result<U, mixed>
 */
function andThen(Closure $callback): Closure
{
    // @var Closure(Result<mixed, mixed>): Result<U, mixed>
    return static fn (Result $result): Result => $result->andThen($callback);
}

/**
 * Pipeline function: Handles Err by calling a Result-returning function.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\orElse(fn($e) => recover($e))
 *
 * @template F
 * @param  Closure(mixed): Result<mixed, F>                $callback
 * @return Closure(Result<mixed, mixed>): Result<mixed, F>
 */
function orElse(Closure $callback): Closure
{
    // @var Closure(Result<mixed, mixed>): Result<mixed, F>
    return static fn (Result $result): Result => $result->orElse($callback);
}

/**
 * Pipeline function: Performs a side-effect on Ok value, passing through the Result.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\inspect(fn($x) => logger()->info("Got: {$x}"))
 *
 * @param  Closure(mixed): mixed                               $callback
 * @return Closure(Result<mixed, mixed>): Result<mixed, mixed>
 */
function inspect(Closure $callback): Closure
{
    // @var Closure(Result<mixed, mixed>): Result<mixed, mixed>
    return static fn (Result $result): Result => $result->inspect($callback);
}

/**
 * Pipeline function: Performs a side-effect on Err value, passing through the Result.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\inspectErr(fn($e) => logger()->error($e))
 *
 * @param  Closure(mixed): mixed                               $callback
 * @return Closure(Result<mixed, mixed>): Result<mixed, mixed>
 */
function inspectErr(Closure $callback): Closure
{
    // @var Closure(Result<mixed, mixed>): Result<mixed, mixed>
    return static fn (Result $result): Result => $result->inspectErr($callback);
}

/**
 * Pipeline function: Unwraps the Ok value or returns the default.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\unwrapOr(0)
 *
 * @template U
 * @param  U                                    $default
 * @return Closure(Result<mixed, mixed>): mixed
 */
function unwrapOr(mixed $default): Closure
{
    return static fn (Result $result): mixed => $result->unwrapOr($default);
}

/**
 * Pipeline function: Unwraps the Ok value or computes a default from the Err.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Result\unwrapOrElse(fn($e) => fallback($e))
 *
 * @param  Closure(mixed): mixed                $callback
 * @return Closure(Result<mixed, mixed>): mixed
 */
function unwrapOrElse(Closure $callback): Closure
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
