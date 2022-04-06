<?php declare(strict_types=1);

namespace Star\Component\Specification;

use Star\Component\Type\Value;

interface SpecificationPlatform
{
    public function applyAndX(Specification $first, Specification $second, Specification ...$others): void;

    public function applyOrX(Specification $first, Specification $second, Specification ...$others): void;

    public function applyEquals(string $alias, string $property, Value $value): void;
}
