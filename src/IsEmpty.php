<?php declare(strict_types=1);

namespace Star\Component\Specification;

final class IsEmpty implements Specification
{
    private string $alias;
    private string $property;

    public function __construct(string $alias, string $property)
    {
        $this->alias = $alias;
        $this->property = $property;
    }

    public function applySpecification(SpecificationPlatform $platform): void
    {
        $platform->applyIsEmpty($this->alias, $this->property);
    }
}
