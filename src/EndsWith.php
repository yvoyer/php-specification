<?php declare(strict_types=1);

namespace Star\Component\Specification;

use Star\Component\Type\StringValue;

final class EndsWith extends SpecificationWithProperty
{
    public function applySpecification(SpecificationPlatform $platform): void
    {
        $platform->applyEndsWith($this->alias, $this->property, $this->value->toString());
    }

    public static function string(string $alias, string $property, string $value): Specification
    {
        return new self($alias, $property, StringValue::fromString($value));
    }
}
