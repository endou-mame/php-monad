<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad;

use Closure;

/**
 * Pipes a value through a series of callables.
 * Alternative to PHP 8.5's pipeline operator for earlier PHP versions.
 *
 * Usage:
 *   pipe(
 *       Result\ok($input),
 *       Result\map(fn($x) => $x * 2),
 *       Result\andThen(fn($x) => validate($x)),
 *       Result\inspect(fn($x) => log($x)),
 *       Result\unwrapOr(0),
 *   );
 *
 * @param Closure|callable ...$callbacks
 */
function pipe(mixed $value, callable ...$callbacks): mixed
{
    foreach ($callbacks as $callback) {
        $value = $callback($value);
    }

    return $value;
}
