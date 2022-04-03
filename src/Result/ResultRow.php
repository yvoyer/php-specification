<?php declare(strict_types=1);

namespace Star\Component\Specification\Result;

use Star\Component\Type\Value;

interface ResultRow
{
    public function getValue(string $column): Value;
    public function isEmpty(): bool;
}
