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
 * @template U
 * @param  Closure(mixed): U                 $callback
 * @return Closure(Option<mixed>): Option<U>
 */
function map(Closure $callback): Closure
{
    // @var Closure(Option<mixed>): Option<U>
    return static fn (Option $option): Option => $option->map($callback);
}

/**
 * Pipeline function: Chains an Option-returning operation on Some value.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\andThen(fn($x) => findUser($x))
 *
 * @template U
 * @param  Closure(mixed): Option<U>         $callback
 * @return Closure(Option<mixed>): Option<U>
 */
function andThen(Closure $callback): Closure
{
    // @var Closure(Option<mixed>): Option<U>
    return static fn (Option $option): Option => $option->andThen($callback);
}

/**
 * Pipeline function: Handles None by calling an Option-returning function.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\orElse(fn() => getDefault())
 *
 * @template U
 * @param  Closure(): Option<U>                  $callback
 * @return Closure(Option<mixed>): Option<mixed>
 */
function orElse(Closure $callback): Closure
{
    // @var Closure(Option<mixed>): Option<mixed>
    return static fn (Option $option): Option => $option->orElse($callback);
}

/**
 * Pipeline function: Filters Some value by predicate, returning None if false.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\filter(fn($x) => $x > 0)
 *
 * @param  Closure(mixed): bool                  $predicate
 * @return Closure(Option<mixed>): Option<mixed>
 */
function filter(Closure $predicate): Closure
{
    // @var Closure(Option<mixed>): Option<mixed>
    return static fn (Option $option): Option => $option->filter($predicate);
}

/**
 * Pipeline function: Performs a side-effect on Some value, passing through the Option.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\inspect(fn($x) => logger()->info("Got: {$x}"))
 *
 * @param  Closure(mixed): mixed                 $callback
 * @return Closure(Option<mixed>): Option<mixed>
 */
function inspect(Closure $callback): Closure
{
    // @var Closure(Option<mixed>): Option<mixed>
    return static fn (Option $option): Option => $option->inspect($callback);
}

/**
 * Pipeline function: Unwraps the Some value or returns the default.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\unwrapOr(0)
 *
 * @template U
 * @param  U                             $default
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
 * @param  Closure(): mixed              $callback
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
 * Curried form of traverse for use with the pipeline operator (|>).
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\traverse_with(fn($x) => validate($x))
 *
 * @template T
 * @template U
 * @template E
 *
 * @param (Closure(T): Result<U, E>) $fn
 *
 * @return (Closure(Option<T>): Result<U|null, E>)
 */
function traverse_with(Closure $fn): Closure
{
    return static fn (Option $option): Result => traverse($option, $fn);
}

/**
 * Conditionally apply a bind operation based on an Option value.
 *
 * If Some, applies $fn(unwrapped value) which must return a Closure
 * suitable for andThen(). If None, returns an identity function.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $result |> Option\apply_if_some($option, fn($v) => fn($x) => doSomething($v, $x))
 *
 * @template T
 * @template V
 * @template W
 * @template E
 *
 * @param Option<T>                                $option
 * @param (Closure(T): (Closure(V): Result<W, E>)) $fn
 *
 * @return (Closure(Result<V, E>): Result<V|W, E>)
 */
function apply_if_some(Option $option, Closure $fn): Closure
{
    // @phpstan-ignore return.type
    return $option->mapOrElse(
        static function (mixed $value) use ($fn): Closure {
            $binding = $fn($value);

            return \EndouMame\PhpMonad\Result\andThen($binding);
        },
        static fn (): Closure => static fn (Result $result): Result => $result,
    );
}

/**
 * Pipeline function: Converts Option to Result with a fixed error value.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\okOr('not found')
 *
 * @template E
 * @param  E                                        $err
 * @return Closure(Option<mixed>): Result<mixed, E>
 */
function okOr(mixed $err): Closure
{
    // @var Closure(Option<mixed>): Result<mixed, E>
    return static fn (Option $option): Result => $option->okOr($err);
}

/**
 * Pipeline function: Converts Option to Result with a lazy error value.
 *
 * Usage with PHP 8.5 pipeline operator:
 *   $option |> Option\okOrElse(fn() => new NotFoundException())
 *
 * @template E
 * @param  Closure(): E                             $err
 * @return Closure(Option<mixed>): Result<mixed, E>
 */
function okOrElse(Closure $err): Closure
{
    // @var Closure(Option<mixed>): Result<mixed, E>
    return static fn (Option $option): Result => $option->okOrElse($err);
}
