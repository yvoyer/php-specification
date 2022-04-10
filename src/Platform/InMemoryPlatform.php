<?php declare(strict_types=1);

namespace Star\Component\Specification\Platform;

use Star\Component\Specification\Result\ArrayResult;
use Star\Component\Specification\Result\EmptyRow;
use Star\Component\Specification\Result\NotUniqueResult;
use Star\Component\Specification\Result\ResultRow;
use Star\Component\Specification\Result\ResultSet;
use Star\Component\Specification\Specification;
use Star\Component\Specification\SpecificationPlatform;
use Star\Component\Type\NullValue;
use function array_filter;
use function array_merge;
use function count;
use function in_array;
use function mb_stripos;
use function mb_strlen;
use function sprintf;
use function usort;

final class InMemoryPlatform implements SpecificationPlatform
{
    /**
     * @var callable[]
     */
    private array $constraints = [];

    /**
     * @var string[] The direction to sort indexed by properties.
     */
    private array $sorters = [];

    public function applyAndX(Specification $first, Specification $second, Specification ...$others): void
    {
        $specifications = array_merge([$first, $second], $others);
        $collector = new self();
        foreach ($specifications as $specification) {
            $specification->applySpecification($collector);
        }

        $this->constraints[] = function (ResultRow $row) use ($collector) : bool {
            foreach ($collector->constraints as $callable) {
                if (! $callable($row)) {
                    return false;
                }
            }

            return true;
        };
        $this->sorters = array_merge($this->sorters, $collector->sorters);
    }

    public function applyOrX(Specification $first, Specification $second, Specification ...$others): void
    {
        $specifications = array_merge([$first, $second], $others);
        $collector = new self();
        foreach ($specifications as $specification) {
            $specification->applySpecification($collector);
        }

        $this->constraints[] = function (ResultRow $row) use ($collector) : bool {
            foreach ($collector->constraints as $callable) {
                if ($callable($row)) {
                    return true;
                }
            }

            return false;
        };
        $this->sorters = array_merge($this->sorters, $collector->sorters);
    }

    public function applyContains(string $alias, string $property, string $value): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $value): bool {
            return false !== mb_stripos($row->getValue($property)->toString(), $value);
        };
    }

    public function applyEndsWith(string $alias, string $property, string $value): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $value): bool {
            $propertyValue = $row->getValue($property)->toString();
            $expectedPosition = mb_strlen($propertyValue) - mb_strlen($value);

            return $expectedPosition === mb_stripos($propertyValue, $value);
        };
    }

    public function applyEqualsString(string $alias, string $property, string $value): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $value): bool {
            return $value === $row->getValue($property)->toString();
        };
    }

    public function applyEqualsInteger(string $alias, string $property, int $value): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $value): bool {
            return $value === $row->getValue($property)->toInteger();
        };
    }

    public function applyEqualsFloat(string $alias, string $property, float $value): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $value): bool {
            return $value === $row->getValue($property)->toFloat();
        };
    }

    public function applyEqualsBoolean(string $alias, string $property, bool $value): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $value): bool {
            return $value === $row->getValue($property)->toBool();
        };
    }

    public function applyGreater(string $alias, string $property, float $value): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $value): bool {
            return $row->getValue($property)->toFloat() > $value;
        };
    }

    public function applyGreaterEquals(string $alias, string $property, float $value): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $value): bool {
            return $row->getValue($property)->toFloat() >= $value;
        };
    }

    public function applyInStrings(string $alias, string $property, string ...$values): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $values): bool {
            return in_array($row->getValue($property)->toString(), $values, true);
        };
    }

    public function applyInIntegers(string $alias, string $property, int ...$values): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $values): bool {
            return in_array($row->getValue($property)->toInteger(), $values, true);
        };
    }

    public function applyInFloats(string $alias, string $property, float ...$values): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $values): bool {
            return in_array($row->getValue($property)->toFloat(), $values, true);
        };
    }

    public function applyIsNull(string $alias, string $property): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property): bool {
            return $row->getValue($property) instanceof NullValue;
        };
    }

    public function applyIsEmpty(string $alias, string $property): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property): bool {
            return mb_strlen($row->getValue($property)->toString()) === 0;
        };
    }

    public function applyLower(string $alias, string $property, float $value): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $value): bool {
            return $row->getValue($property)->toFloat() < $value;
        };
    }

    public function applyLowerEquals(string $alias, string $property, float $value): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $value): bool {
            return $row->getValue($property)->toFloat() <= $value;
        };
    }

    public function applyNot(Specification $specification): void
    {
        $collector = new InMemoryPlatform();
        $specification->applySpecification($collector);
        $constraints = $collector->constraints;

        $this->constraints[] = function (ResultRow $row) use ($constraints): bool {
            $return = true;
            foreach ($constraints as $constraint) {
                if ($constraint($row)) {
                    $return = false;
                }
            }

            return $return;
        };
    }

    public function applyOrderAsc(string $alias, string $property): void
    {
        $this->sorters[$property] = 'ASC';
    }

    public function applyOrderDesc(string $alias, string $property): void
    {
        $this->sorters[$property] = 'DESC';
    }

    public function applyStartsWith(string $alias, string $property, string $value): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $value): bool {
            return 0 === mb_stripos($row->getValue($property)->toString(), $value);
        };
    }

    /**
     * @param ResultRow ...$data
     * @return ResultSet
     * @internal Private method used by the NativePlatform system, should not be used externally.
     */
    public function executeFetchAll(ResultRow ...$data): ResultSet
    {
        $result = $data;
        foreach ($this->constraints as $callable) {
            $result = array_filter($result, $callable);
        }

        if (count($this->sorters) > 0) {
            usort(
                $result,
                function (ResultRow $left, ResultRow $right): int {
                    $return = 1;

                    // weight each property
                    $weight = pow(10, count($this->sorters));
                    foreach ($this->sorters as $property => $direction) {
                        $leftValue = $left->getValue($property)->toString();
                        $rightValue = $right->getValue($property)->toString();

                        if ($direction === 'ASC') {
                            $return += ($leftValue <=> $rightValue) * $weight;
                        }

                        if ($direction === 'DESC') {
                            $return -= ($leftValue <=> $rightValue) * $weight;
                        }

                        $weight /= 10;
                    }

                    return $return;
                }
            );
        }

        return ArrayResult::fromRows(...$result);
    }

    /**
     * @param ResultRow ...$data
     * @return ResultRow
     * @internal Private method used by the NativePlatform system, should not be used externally.
     */
    public function executeFetchOne(ResultRow ...$data): ResultRow
    {
        $result = $this->executeFetchAll(...$data);
        if ($result->isEmpty()) {
            return new EmptyRow();
        }

        if (count($result) === 1) {
            return $result->getRow(0);
        }

        throw new NotUniqueResult(
            sprintf('Query was expected to return 0-1 row, "%s" rows returned.', $result->count())
        );
    }

    /**
     * @param ResultRow ...$data
     * @return bool
     * @internal Private method used by the NativePlatform system, should not be used externally.
     */
    public function executeExists(ResultRow ...$data): bool
    {
        return ! $this->executeFetchAll(...$data)->isEmpty();
    }
}
