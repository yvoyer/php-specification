<?php declare(strict_types=1);

namespace Star\Component\Specification;

use Star\Component\Type\BooleanValue;
use Star\Component\Type\FloatValue;
use Star\Component\Type\IntegerValue;
use Star\Component\Type\StringValue;
use Star\Component\Type\ValueGuesser;

final class EqualsTo extends SpecificationWithProperty
{
    public function applySpecification(SpecificationPlatform $platform): void
    {
        $platform->applyEquals($this->alias, $this->property, $this->value);
    }

    /**
     * @param string $alias
     * @param string $property
     * @param mixed $value
     * @return Specification
     */
    public static function mixedValue(string $alias, string $property, $value): Specification
    {
        return new self($alias, $property, ValueGuesser::fromMixed($value));
    }

    public static function integerValue(string $alias, string $property, int $value): Specification
    {
        return new self($alias, $property, IntegerValue::fromInteger($value));
    }

    public static function floatValue(string $alias, string $property, float $value): Specification
    {
        return new self($alias, $property, FloatValue::fromFloat($value));
    }

    public static function booleanValue(string $alias, string $property, bool $value): Specification
    {
        return new self($alias, $property, BooleanValue::fromBoolean($value));
    }

    public static function stringValue(string $alias, string $property, string $value): Specification
    {
        return new self($alias, $property, StringValue::fromString($value));
    }
}
