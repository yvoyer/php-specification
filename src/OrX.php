<?php declare(strict_types=1);

namespace Star\Component\Specification;

use function array_merge;

final class OrX implements Specification
{
    /**
     * @var Specification[]
     */
    private array $specifications;

    public function __construct(Specification $first, Specification $second, Specification ...$others)
    {
        $this->specifications = array_merge([$first], [$second], $others);
    }

    public function applySpecification(SpecificationPlatform $platform): void
    {
        $platform->applyOrX(...$this->specifications);
    }
}
