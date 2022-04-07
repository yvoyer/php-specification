<?php declare(strict_types=1);

namespace Star\Component\Specification;

use Star\Component\Type\Value;

abstract class SpecificationWithProperty implements Specification
{
    protected string $alias;
    protected string $property;
    protected Value $value;

    public function __construct(string $alias, string $property, Value $value)
    {
        $this->alias = $alias;
        $this->property = $property;
        $this->value = $value;
    }
}
