<?php declare(strict_types=1);

namespace Star\Component\Specification;

use Star\Component\Type\FloatValue;
use Star\Component\Type\IntegerValue;

final class Greater extends SpecificationWithProperty
{
    public function applySpecification(SpecificationPlatform $platform): void
    {
        $platform->applyGreater($this->alias, $this->property, $this->value);
    }

    public static function thanInteger(string $alias, string $property, int $value): Specification
    {
        return new self($alias, $property, IntegerValue::fromInteger($value));
    }

    public static function thanFloat(string $alias, string $property, float $value): Specification
    {
        return new self($alias, $property, FloatValue::fromFloat($value));
    }
    // todo thanDate()
}
