<?php declare(strict_types=1);

namespace Star\Component\Specification\Result;

use Star\Component\Type\Value;
use Star\Component\Type\ValueGuesser;
use Webmozart\Assert\Assert;
use function array_keys;

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
        Assert::allIsInstanceOf(
            $row,
            Value::class,
            'Row must be given a map of "%2$s" indexed by column code, got: "%s".'
        );
        Assert::allString(
            array_keys($row),
            'Row must be given a string index as column identifier, got "%s".'
        );
        $this->row = $row;
    }

    public function getValue(string $column): Value
    {
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
