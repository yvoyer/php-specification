<?php declare(strict_types=1);

namespace Star\Component\Specification\Result;

use Countable;
use IteratorAggregate;
use Star\Component\Type\Value;

/**
 * @extends IteratorAggregate<int, ResultRow>
 */
interface ResultSet extends Countable, IteratorAggregate
{
    public function getRow(int $row): ResultRow;
    public function getValue(int $row, string $column): Value;
    public function isEmpty(): bool;
}
