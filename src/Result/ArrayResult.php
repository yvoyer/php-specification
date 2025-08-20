<?php declare(strict_types=1);

namespace Star\Component\Specification\Result;

use Star\Component\Specification\Datasource;
use Star\Component\Specification\Platform\InMemoryPlatform;
use Star\Component\Specification\Specification;
use Star\Component\Type\Value;
use Traversable;
use function array_key_exists;
use function array_map;

final class ArrayResult implements ResultSet, Datasource
{
    /**
     * @var ResultRow[]
     */
    private array $rows;

    private function __construct(ResultRow ...$rows)
    {
        $this->rows = $rows;
    }

    public function fetchAll(Specification $specification): ResultSet
    {
        $platform = new InMemoryPlatform();
        $specification->applySpecification($platform);

        return $platform->executeFetchAll(...$this->rows);
    }

    public function fetchOne(Specification $specification): ResultRow
    {
        $platform = new InMemoryPlatform();
        $specification->applySpecification($platform);

        return $platform->executeFetchOne(...$this->rows);
    }

    public function exists(Specification $specification): bool
    {
        $platform = new InMemoryPlatform();
        $specification->applySpecification($platform);

        return $platform->executeExists(...$this->rows);
    }

    public function count(): int
    {
        return count($this->rows);
    }

    public function getRow(int $row): ResultRow
    {
        if (! array_key_exists($row, $this->rows)) {
            $this->rows[$row] = new EmptyRow();
        }

        return $this->rows[$row];
    }

    public function getValue(int $row, string $column): Value
    {
        return $this->getRow($row)->getValue($column);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function getIterator(): Traversable
    {
        foreach ($this->rows as $row) {
            yield $row;
        }
    }

    /**
     * @param callable $callback
     * @return mixed[]
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->rows);
    }

    /**
     * @param mixed[] ...$rows
     * @return static
     */
    public static function fromRowsOfMixed(array ...$rows): ResultSet
    {
        return self::fromRows(
            ...array_map(
                function (array $row): ResultRow {
                    return ArrayRow::fromMixedMap($row);
                },
                $rows
            )
        );
    }

    /**
     * @param ResultRow ...$rows
     * @return static
     */
    public static function fromRows(ResultRow ...$rows): ResultSet
    {
        return new self(...$rows);
    }
}
