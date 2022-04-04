<?php declare(strict_types=1);

namespace Star\Component\Specification;

use Star\Component\Type\BooleanValue;
use Star\Component\Type\FloatValue;
use Star\Component\Type\IntegerValue;
use Star\Component\Type\StringValue;
use Star\Component\Type\Value;
use Star\Component\Type\ValueGuesser;

final class EqualsTo implements Specification
{
    private string $alias;
    private string $property;
    private Value $value;

    private function __construct(
        string $alias,
        string $property,
        Value $value
    ) {
        $this->alias = $alias;
        $this->property = $property;
        $this->value = $value;
    }

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
    public static function fromMixed(string $alias, string $property, $value): Specification
    {
        return new self($alias, $property, ValueGuesser::fromMixed($value));
    }

    public static function fromInteger(string $alias, string $property, int $value): Specification
    {
        return new self($alias, $property, IntegerValue::fromInteger($value));
    }

    public static function fromFloat(string $alias, string $property, float $value): Specification
    {
        return new self($alias, $property, FloatValue::fromFloat($value));
    }

    public static function fromBoolean(string $alias, string $property, bool $value): Specification
    {
        return new self($alias, $property, BooleanValue::fromBoolean($value));
    }

    public static function fromString(string $alias, string $property, string $value): Specification
    {
        return new self($alias, $property, StringValue::fromString($value));
    }
}
