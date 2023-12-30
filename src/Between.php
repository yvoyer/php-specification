<?php declare(strict_types=1);

namespace Star\Component\Specification;

use DateTimeInterface;
use Star\Component\Type\DateTimeValue;
use Star\Component\Type\FloatValue;
use Star\Component\Type\IntegerValue;
use Star\Component\Type\Value;

final class Between implements Specification
{
    private string $alias;
    private string $property;
    private Value $leftValue;
    private Value $rightValue;

    public function __construct(
        string $alias,
        string $property,
        Value  $leftValue,
        Value  $rightValue
    ) {
        $this->alias = $alias;
        $this->property = $property;
        $this->leftValue = $leftValue;
        $this->rightValue = $rightValue;
    }

    public function applySpecification(SpecificationPlatform $platform): void
    {
        $platform->applyAndX(
            new GreaterEquals($this->alias, $this->property, $this->leftValue),
            new LowerEquals($this->alias, $this->property, $this->rightValue)
        );
    }

    public static function integers(
        string $alias,
        string $property,
        int $leftValue,
        int $rightValue
    ): Specification {
        return new self(
            $alias,
            $property,
            IntegerValue::fromInteger($leftValue),
            IntegerValue::fromInteger($rightValue)
        );
    }

    public static function floats(
        string $alias,
        string $property,
        float $leftValue,
        float $rightValue
    ): Specification {
        return new self(
            $alias,
            $property,
            FloatValue::fromFloat($leftValue),
            FloatValue::fromFloat($rightValue)
        );
    }

    public static function dates(
        string $alias,
        string $property,
        DateTimeInterface $leftValue,
        DateTimeInterface $rightValue
    ): Specification {
        return new self(
            $alias,
            $property,
            DateTimeValue::fromDateTime($leftValue),
            DateTimeValue::fromDateTime($rightValue)
        );
    }
}
