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
    private string $types;

    /**
     * @var Value[]
     */
    private array $values;

    private function __construct(string $alias, string $property, string $types, Value ...$values)
    {
        $this->alias = $alias;
        $this->property = $property;
        $this->types = $types;
        $this->values = $values;
    }

    public function applySpecification(SpecificationPlatform $platform): void
    {
        switch ($this->types) {
            case 'string':
                $platform->applyInStrings(
                    $this->alias,
                    $this->property,
                    ...array_map(
                        function (Value $value): string {
                            return $value->toString();
                        },
                        $this->values
                    )
                );
                break;

            case 'integer':
                $platform->applyInIntegers(
                    $this->alias,
                    $this->property,
                    ...array_map(
                        function (Value $value): int {
                            return $value->toInteger();
                        },
                        $this->values
                    )
                );
                break;

            case 'float':
                $platform->applyInFloats(
                    $this->alias,
                    $this->property,
                    ...array_map(
                        function (Value $value): float {
                            return $value->toFloat();
                        },
                        $this->values
                    )
                );
                break;
        }
    }

    public static function ofIntegers(string $alias, string $property, int $first, int ...$others): Specification
    {
        return new self(
            $alias,
            $property,
            'integer',
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
        return new self(
            $alias,
            $property,
            'float',
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
        return new self(
            $alias,
            $property,
            'string',
            ...array_map(
                function (string $value): Value {
                    return StringValue::fromString($value);
                },
                array_merge([$first], $others)
            )
        );
    }
}
