<?php declare(strict_types=1);

namespace Star\Component\Specification\Adapter\NativePHP;

use Star\Component\Specification\Result\ArrayResult;
use Star\Component\Specification\Result\EmptyRow;
use Star\Component\Specification\Result\NotUniqueResult;
use Star\Component\Specification\Result\ResultRow;
use Star\Component\Specification\Result\ResultSet;
use Star\Component\Specification\SpecificationBuilder;
use Star\Component\Type\Value;
use function array_filter;
use function count;
use function sprintf;

final class CallableConstraintBuilder implements SpecificationBuilder
{
    /**
     * @var callable[]
     */
    private array $constraints = [];

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
