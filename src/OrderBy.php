<?php declare(strict_types=1);

namespace Star\Component\Specification;

use InvalidArgumentException;
use function in_array;
use function sprintf;
use function strtoupper;

final class OrderBy implements Specification
{
    private string $alias;
    private string $property;
    private string $direction;

    public function __construct(string $alias, string $property, string $direction)
    {
        $this->alias = $alias;
        $this->property = $property;
        $this->direction = strtoupper($direction);

        if (! in_array($this->direction, ['ASC', 'DESC'])) {
            throw new InvalidArgumentException(
                sprintf('The direction order "%s" is not supported.', $direction)
            );
        }
    }

    public function applySpecification(SpecificationPlatform $platform): void
    {
        switch ($this->direction) {
            case 'ASC':
                $platform->applyOrderAsc($this->alias, $this->property);
                return;

            case 'DESC':
                $platform->applyOrderDesc($this->alias, $this->property);
                return;
        }

        throw new InvalidArgumentException(
            sprintf('The direction order "%s" is not supported.', $this->direction)
        );
    }

    public static function asc(string $alias, string $property): Specification
    {
        return new self($alias, $property, 'ASC');
    }

    public static function desc(string $alias, string $property): Specification
    {
        return new self($alias, $property, 'DESC');
    }
}
