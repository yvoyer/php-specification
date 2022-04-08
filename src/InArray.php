<?php declare(strict_types=1);

namespace Star\Component\Specification;

use Star\Component\Type\FloatValue;
use Star\Component\Type\IntegerValue;
use Star\Component\Type\StringValue;
use Star\Component\Type\Value;
use function array_map;
use function array_merge;

final class InArray implements Specification
{
    private string $alias;
    private string $property;

    /**
     * @var Value[]
     */
    private array $values;

    private function __construct(string $alias, string $property, Value ...$values)
    {
        $this->alias = $alias;
        $this->property = $property;
        $this->values = $values;
    }

    public function applySpecification(SpecificationPlatform $platform): void
    {
        $platform->applyIn($this->alias, $this->property, ...$this->values);
    }

    public static function ofValues(string $alias, string $property, Value $first, Value ...$others): Specification
    {
        return new self($alias, $property, ...array_merge([$first], $others));
    }

    public static function ofIntegers(string $alias, string $property, int $first, int ...$others): Specification
    {
        return self::ofValues(
            $alias,
            $property,
            ...array_map(
                function (int $value): Value {
                    return IntegerValue::fromInteger($value);
                },
                array_merge([$first], $others)
            )
        );
    }

    public static function ofFloats(string $alias, string $property, float $first, float ...$others): Specification
    {
        return self::ofValues(
            $alias,
            $property,
            ...array_map(
                function (float $value): Value {
                    return FloatValue::fromFloat($value);
                },
                array_merge([$first], $others)
            )
        );
    }

    public static function ofStrings(string $alias, string $property, string $first, string ...$others): Specification
    {
        return self::ofValues(
            $alias,
            $property,
            ...array_map(
                function (string $value): Value {
                    return StringValue::fromString($value);
                },
                array_merge([$first], $others)
            )
        );
    }
}
