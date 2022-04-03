<?php declare(strict_types=1);

namespace Star\Component\Specification\Result;

use Star\Component\Specification\Adapter\NativePHP\CallableConstraintBuilder;
use Star\Component\Specification\Datasource;
use Star\Component\Specification\Specification;
use Star\Component\Type\Value;
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
        $builder = new CallableConstraintBuilder();
        $specification->applySpecification($builder);

        return $builder->executeFetchAll(...$this->rows);
    }

    public function fetchOne(Specification $specification): ResultRow
    {
        $builder = new CallableConstraintBuilder();
        $specification->applySpecification($builder);

        return $builder->executeFetchOne(...$this->rows);
    }

    public function exists(Specification $specification): bool
    {
        $builder = new CallableConstraintBuilder();
        $specification->applySpecification($builder);

        return $builder->executeExists(...$this->rows);
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

    /**
     * @param mixed[] ...$rows
     * @return static
     */
    public static function fromArray(array ...$rows): ResultSet
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
