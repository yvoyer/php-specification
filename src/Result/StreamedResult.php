<?php declare(strict_types=1);

namespace Star\Component\Specification\Result;

use Star\Component\Type\Value;
use Traversable;

final class StreamedResult implements ResultSet
{
    public function count(): int
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented yet.');
    }

    public function getValue(int $row, string $column): Value
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented yet.');
    }

    public function getRow(int $row): ResultRow
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented yet.');
    }

    public function isEmpty(): bool
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented yet.');
    }

    public function getIterator(): Traversable
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented yet.');
    }
}
