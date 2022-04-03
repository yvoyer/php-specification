<?php declare(strict_types=1);

namespace Star\Component\Specification\Result;

use Countable;
use Star\Component\Type\Value;

interface ResultSet extends Countable
{
    public function getRow(int $row): ResultRow;
    public function getValue(int $row, string $column): Value;
    public function isEmpty(): bool;
}
