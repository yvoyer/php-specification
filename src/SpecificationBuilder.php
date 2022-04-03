<?php declare(strict_types=1);

namespace Star\Component\Specification;

use Star\Component\Type\Value;

interface SpecificationBuilder
{
    public function applyEquals(string $alias, string $property, Value $value): void;
}
