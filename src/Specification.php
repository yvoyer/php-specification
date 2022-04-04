<?php declare(strict_types=1);

namespace Star\Component\Specification;

interface Specification
{
    public function applySpecification(SpecificationPlatform $platform): void;
}
