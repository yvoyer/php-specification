<?php declare(strict_types=1);

namespace Star\Component\Specification;

use Star\Component\Type\BooleanValue;
use Star\Component\Type\FloatValue;
use Star\Component\Type\IntegerValue;
use Star\Component\Type\StringValue;
use Star\Component\Type\ValueVisitor;

final class EqualsTo extends SpecificationWithProperty
{
    public function applySpecification(SpecificationPlatform $platform): void
    {
        $this->value->acceptValueVisitor(
            new class($this->alias, $this->property, $platform) implements ValueVisitor {
                private string $alias;
                private string $property;
                private SpecificationPlatform $platform;

                public function __construct(string $alias, string $property, SpecificationPlatform $platform)
                {
                    $this->alias = $alias;
                    $this->property = $property;
                    $this->platform = $platform;
                }

                public function visitStringValue(string $value): void
                {
                    $this->platform->applyEqualsString($this->alias, $this->property, $value);
                }

                public function visitIntegerValue(int $value): void
                {
                    $this->platform->applyEqualsInteger($this->alias, $this->property, $value);
                }

                public function visitFloatValue(float $value): void
                {
                    $this->platform->applyEqualsFloat($this->alias, $this->property, $value);
                }

                public function visitBooleanValue(bool $value): void
                {
                    $this->platform->applyEqualsBoolean($this->alias, $this->property, $value);
                }

                public function visitNullValue(): void
                {
                    throw new \RuntimeException(__METHOD__ . ' not implemented yet.');
                }

                public function visitObjectValue(object $value): void
                {
                    throw new \RuntimeException(__METHOD__ . ' not implemented yet.');
                }

                public function visitArrayOfStrings(string ...$values): void
                {
                    throw new \RuntimeException(__METHOD__ . ' not implemented yet.');
                }

                public function visitArrayOfIntegers(int ...$values): void
                {
                    throw new \RuntimeException(__METHOD__ . ' not implemented yet.');
                }

                public function visitArrayOfFloats(float ...$values): void
                {
                    throw new \RuntimeException(__METHOD__ . ' not implemented yet.');
                }

                public function visitArrayOfBooleans(bool ...$values): void
                {
                    throw new \RuntimeException(__METHOD__ . ' not implemented yet.');
                }

                public function visitArrayOfObjects(object ...$values): void
                {
                    throw new \RuntimeException(__METHOD__ . ' not implemented yet.');
                }
            }
        );
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
