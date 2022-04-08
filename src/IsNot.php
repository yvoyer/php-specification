<?php declare(strict_types=1);

namespace Star\Component\Specification;

final class IsNot implements Specification
{
    private Specification $specification;

    public function __construct(Specification $specification)
    {
        $this->specification = $specification;
    }

    public function applySpecification(SpecificationPlatform $platform): void
    {
        $platform->applyNot($this->specification);
    }
}
