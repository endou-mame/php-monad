<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Option;

use Closure;
use EndouMame\PhpMonad\Option;
use EndouMame\PhpMonad\Result;

/**
 * Pipeline function: Maps the Some value using the callback.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\map(fn($x) => $x * 2)
 *
 * @template T
 * @template U
 * @param  Closure(T): U                 $callback
 * @return Closure(Option<T>): Option<U>
 */
function map(Closure $callback): Closure
{
    return static fn (Option $option): Option => $option->map($callback);
}

/**
 * Pipeline function: Chains an Option-returning operation on Some value.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\andThen(fn($x) => findUser($x))
 *
 * @template T
 * @template U
 * @param  Closure(T): Option<U>         $callback
 * @return Closure(Option<T>): Option<U>
 */
function andThen(Closure $callback): Closure
{
    return static fn (Option $option): Option => $option->andThen($callback);
}

/**
 * Pipeline function: Handles None by calling an Option-returning function.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\orElse(fn() => getDefault())
 *
 * @template U
 * @param  Closure(): Option<U>                    $callback
 * @return Closure(Option<mixed>): Option<mixed|U>
 */
function orElse(Closure $callback): Closure
{
    return static fn (Option $option): Option => $option->orElse($callback);
}

/**
 * Pipeline function: Filters Some value by predicate, returning None if false.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\filter(fn($x) => $x > 0)
 *
 * @param  Closure(mixed): bool                    $predicate
 * @return Closure(Option<mixed>): Option<mixed>
 */
function filter(Closure $predicate): Closure
{
    return static fn (Option $option): Option => $option->filter($predicate);
}

/**
 * Pipeline function: Performs a side-effect on Some value, passing through the Option.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\inspect(fn($x) => logger()->info("Got: {$x}"))
 *
 * @param  Closure(mixed): mixed                   $callback
 * @return Closure(Option<mixed>): Option<mixed>
 */
function inspect(Closure $callback): Closure
{
    return static fn (Option $option): Option => $option->inspect($callback);
}

/**
 * Pipeline function: Unwraps the Some value or returns the default.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\unwrapOr(0)
 *
 * @template U
 * @param  U                               $default
 * @return Closure(Option<mixed>): mixed
 */
function unwrapOr(mixed $default): Closure
{
    return static fn (Option $option): mixed => $option->unwrapOr($default);
}

/**
 * Pipeline function: Unwraps the Some value or computes a default.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\unwrapOrElse(fn() => computeDefault())
 *
 * @param  Closure(): mixed                $callback
 * @return Closure(Option<mixed>): mixed
 */
function unwrapOrElse(Closure $callback): Closure
{
    return static fn (Option $option): mixed => $option->unwrapOrElse($callback);
}

/**
 * Pipeline function: Unwraps the Some value or throws RuntimeException with the message.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\expect('Value must be present')
 *
 * @return Closure(Option<mixed>): mixed
 */
function expect(string $message): Closure
{
    return static fn (Option $option): mixed => $option->expect($message);
}

/**
 * Pipeline function: Converts Option to Result with a fixed error value.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\okOr('not found')
 *
 * @template E
 * @param  E                                          $err
 * @return Closure(Option<mixed>): Result<mixed, E>
 */
function okOr(mixed $err): Closure
{
    return static fn (Option $option): Result => $option->okOr($err);
}

/**
 * Pipeline function: Converts Option to Result with a lazy error value.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\okOrElse(fn() => new NotFoundException())
 *
 * @template E
 * @param  Closure(): E                               $err
 * @return Closure(Option<mixed>): Result<mixed, E>
 */
function okOrElse(Closure $err): Closure
{
    return static fn (Option $option): Result => $option->okOrElse($err);
}
