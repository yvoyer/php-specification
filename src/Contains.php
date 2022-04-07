<?php declare(strict_types=1);

namespace Star\Component\Specification;

use Star\Component\Type\StringValue;
use Star\Component\Type\Value;

final class Contains extends SpecificationWithProperty
{
    private bool $caseSensitive;

    protected function __construct(string $alias, string $property, Value $value, bool $caseSensitive)
    {
        parent::__construct($alias, $property, $value);
        $this->caseSensitive = $caseSensitive;
    }

    public function applySpecification(SpecificationPlatform $platform): void
    {
        $platform->applyContains($this->alias, $this->property, $this->value, $this->caseSensitive);
    }

    public static function caseSensitiveString(string $alias, string $property, string $value): Specification
    {
        return new self($alias, $property, StringValue::fromString($value), true);
    }

    public static function caseInsensitiveString(string $alias, string $property, string $value): Specification
    {
        return new self($alias, $property, StringValue::fromString($value), false);
    }
}
