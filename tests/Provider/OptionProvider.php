<?php

declare(strict_types=1);

namespace EndouMame\PhpMonad\Tests\Provider;

use EndouMame\PhpMonad\Option;

trait OptionProvider
{
    /**
     * @return iterable<array{Option<mixed>, mixed, mixed}>
     */
    /**
     * @phpstan-ignore missingType.iterableValue
     */
    public static function fromValueMatrix(): iterable
    {
        $o = (object)[];

        yield [Option\none(), null, null];
        yield [Option\some(null), null, 0];

        yield [Option\none(), 0, 0];
        yield [Option\some(0), 0, 1];
        yield [Option\none(), 1, 1];
        yield [Option\some(1), 1, 0];
        yield [Option\some(1), 1, ''];
        yield [Option\some(1), 1, '1'];
        yield [Option\some(1), 1, true];

        yield [Option\none(), [], []];
        yield [Option\some([1]), [1], [2]];

        yield [Option\none(), $o, $o];
        yield [Option\some($o), $o, (object)[]];
    }
}
