<?php declare(strict_types=1);

namespace Star\Component\Specification;

use DateTimeInterface;
use Star\Component\Type\DateTimeValue;
use Star\Component\Type\FloatValue;
use Star\Component\Type\IntegerValue;

final class GreaterEquals extends SpecificationWithProperty
{
    public function applySpecification(SpecificationPlatform $platform): void
    {
        if ($this->value instanceof DateTimeValue) {
            $platform->applyGreaterEqualsToDate($this->alias, $this->property, $this->value->toDate());
            return;
        }

        $platform->applyGreaterEquals($this->alias, $this->property, $this->value->toFloat());
    }

    public static function thanInteger(string $alias, string $property, int $value): Specification
    {
        return new self($alias, $property, IntegerValue::fromInteger($value));
    }

    public static function thanFloat(string $alias, string $property, float $value): Specification
    {
        return new self($alias, $property, FloatValue::fromFloat($value));
    }

    public static function thanDate(string $alias, string $property, DateTimeInterface $value): Specification
    {
        return new self($alias, $property, DateTimeValue::fromDateTime($value));
    }
}
