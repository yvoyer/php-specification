<?php declare(strict_types=1);

namespace Star\Component\Specification\Result;

use Assert\Assertion;
use Star\Component\Type\Value;
use Star\Component\Type\ValueGuesser;
use function array_key_exists;
use function array_keys;
use function implode;
use function sprintf;

final class ArrayRow implements ResultRow
{
    /**
     * @var Value[]
     */
    private array $row;

    /**
     * @param Value[] $row
     */
    private function __construct(array $row)
    {
        Assertion::allIsInstanceOf(
            $row,
            Value::class,
            'Row must be given a map of "%2$s" indexed by column code, got: "%s".'
        );
        Assertion::allString(
            array_keys($row),
            'Row must be given a string index as column identifier, got "%s".'
        );
        $this->row = $row;
    }

    public function getValue(string $column): Value
    {
        if (!array_key_exists($column, $this->row)) {
            throw new ColumnNotFound(
                sprintf(
                    'Column "%s" was not found in result row. Available columns are "%s".',
                    $column,
                    implode(', ', array_keys($this->row))
                )
            );
        }

        return $this->row[$column];
    }

    public function isEmpty(): bool
    {
        return false;
    }

    /**
     * @param mixed[] $map
     * @return ResultRow
     */
    public static function fromMixedMap(array $map): ResultRow
    {
        $normalizedMap = [];
        foreach ($map as $column => $value) {
            if (! $value instanceof Value) {
                $value = ValueGuesser::fromMixed($value);
            }

            $normalizedMap[(string) $column] = $value;
        }

        return new self($normalizedMap);
    }
}
