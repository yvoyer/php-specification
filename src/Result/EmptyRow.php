<?php declare(strict_types=1);

namespace Star\Component\Specification\Result;

use Star\Component\Type\NullValue;
use Star\Component\Type\Value;

final class EmptyRow implements ResultRow
{
    public function getValue(string $column): Value
    {
        return new NullValue();
    }

    public function isEmpty(): bool
    {
        return true;
    }
}
