<?php declare(strict_types=1);

namespace Star\Component\Specification;

final class NullSpecification implements Specification
{
    public function applySpecification(SpecificationPlatform $platform): void
    {
        // do nothing
    }
}
