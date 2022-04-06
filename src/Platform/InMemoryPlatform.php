<?php declare(strict_types=1);

namespace Star\Component\Specification\Platform;

use Star\Component\Specification\Result\ArrayResult;
use Star\Component\Specification\Result\EmptyRow;
use Star\Component\Specification\Result\NotUniqueResult;
use Star\Component\Specification\Result\ResultRow;
use Star\Component\Specification\Result\ResultSet;
use Star\Component\Specification\Specification;
use Star\Component\Specification\SpecificationPlatform;
use Star\Component\Type\Value;
use function array_filter;
use function array_merge;
use function count;
use function mb_stripos;
use function mb_strlen;
use function mb_strpos;
use function sprintf;

final class InMemoryPlatform implements SpecificationPlatform
{
    /**
     * @var callable[]
     */
    private array $constraints = [];

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
    }

    public function applyContains(string $alias, string $property, Value $value, bool $caseSensitive): void
    {
        $stringValue = $value->toString();
        $this->constraints[] = function (ResultRow $row) use ($property, $stringValue, $caseSensitive): bool {
            $propertyValue = $row->getValue($property)->toString();

            if ($caseSensitive) {
                return false !== mb_strpos($propertyValue, $stringValue);
            }

            return false !== mb_stripos($propertyValue, $stringValue);
        };
    }

    public function applyEndsWith(string $alias, string $property, Value $value, bool $caseSensitive): void
    {
        $stringValue = $value->toString();
        $this->constraints[] = function (ResultRow $row) use ($property, $stringValue, $caseSensitive): bool {
            $propertyValue = $row->getValue($property)->toString();
            $expectedPosition = mb_strlen($propertyValue) - mb_strlen($stringValue);

            if ($caseSensitive) {
                return $expectedPosition === mb_strpos($propertyValue, $stringValue);
            }

            return $expectedPosition === mb_stripos($propertyValue, $stringValue);
        };
    }

    public function applyStartsWith(string $alias, string $property, Value $value, bool $caseSensitive): void
    {
        $stringValue = $value->toString();
        $this->constraints[] = function (ResultRow $row) use ($property, $stringValue, $caseSensitive): bool {
            $propertyValue = $row->getValue($property)->toString();

            if ($caseSensitive) {
                return 0 === mb_strpos($propertyValue, $stringValue);
            }

            return 0 === mb_stripos($propertyValue, $stringValue);
        };
    }

    public function applyEquals(string $alias, string $property, Value $value): void
    {
        $this->constraints[] = function (ResultRow $row) use ($property, $value): bool {
            return $value->toString() === $row->getValue($property)->toString();
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
