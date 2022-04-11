<?php declare(strict_types=1);

namespace Star\Component\Specification\Platform;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Star\Component\Specification\Datasource;
use Star\Component\Specification\Result\ArrayResult;
use Star\Component\Specification\Result\ArrayRow;
use Star\Component\Specification\Result\EmptyRow;
use Star\Component\Specification\Result\ResultRow;
use Star\Component\Specification\Result\ResultSet;
use Star\Component\Specification\Specification;
use Star\Component\Specification\SpecificationPlatform;
use function array_merge;
use function count;
use function explode;
use function implode;
use function sprintf;
use function var_dump;

final class DoctrineDBALPlatform implements SpecificationPlatform, Datasource
{
    private QueryBuilder $builder;

    /**
     * @var string[]|CompositeExpression[]
     */
    private array $constraints = [];

    private int $parameterCount = 0;

    private bool $debug;

    public function __construct(QueryBuilder $builder, bool $debug = false)
    {
        $this->builder = $builder;
        $this->debug = $debug;
    }

    private function createCollector(): self
    {
        $collector = new self(new QueryBuilder($this->builder->getConnection()));
        $collector->parameterCount = $this->parameterCount;

        return $collector;
    }

    public function fetchAll(Specification $specification): ResultSet
    {
        return ArrayResult::fromRowsOfMixed(
            ...$this->buildQuery($specification)->fetchAllAssociative()
        );
    }

    public function fetchOne(Specification $specification): ResultRow
    {
        $result = $this->buildQuery($specification)->fetchAssociative();
        if (!$result) {
            return new EmptyRow();
        }

        return ArrayRow::fromMixedMap($result);
    }

    public function exists(Specification $specification): bool
    {
        $this->builder->select('COUNT(*)');

        return (bool) $this->buildQuery($specification)->fetchOne();
    }

    public function applyAndX(Specification $first, Specification $second, Specification ...$others): void
    {
        $this->applyComposite('AND', ...array_merge([$first, $second], $others));
    }

    public function applyOrX(Specification $first, Specification $second, Specification ...$others): void
    {
        $this->applyComposite('OR', ...array_merge([$first, $second], $others));
    }

    public function applyContains(string $alias, string $property, string $value): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->like(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, '%' . $value . '%');
    }

    public function applyEndsWith(string $alias, string $property, string $value): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->like(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, '%' . $value);
    }

    public function applyEqualsString(string $alias, string $property, string $value): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->eq(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, $value);
    }

    public function applyEqualsInteger(string $alias, string $property, int $value): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->eq(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, $value);
    }

    public function applyEqualsFloat(string $alias, string $property, float $value): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->eq(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, $value);
    }

    public function applyEqualsBoolean(string $alias, string $property, bool $value): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->eq(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, $value);
    }

    public function applyGreater(string $alias, string $property, float $value): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->gt(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, $value);
    }

    public function applyGreaterEquals(string $alias, string $property, float $value): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->gte(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, $value);
    }

    public function applyInStrings(string $alias, string $property, string ...$values): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->in(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, $values, Connection::PARAM_STR_ARRAY);
    }

    public function applyInIntegers(string $alias, string $property, int ...$values): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->in(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, $values, Connection::PARAM_INT_ARRAY);
    }

    public function applyInFloats(string $alias, string $property, float ...$values): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->in(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, $values, Connection::PARAM_STR_ARRAY);
    }

    public function applyIsNull(string $alias, string $property): void
    {
        $this->constraints[] = $this->builder->expr()->isNull((sprintf('%s.%s', $alias, $property)));
    }

    public function applyIsEmpty(string $alias, string $property): void
    {
        $expr = $this->builder->expr();
        $this->constraints[] = $expr->or(
            $expr->eq((sprintf('%s.%s', $alias, $property)), $this->builder->expr()->literal('')),
            $expr->isNull((sprintf('%s.%s', $alias, $property)))
        );
    }

    public function applyLower(string $alias, string $property, float $value): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->lt(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, $value);
    }

    public function applyLowerEquals(string $alias, string $property, float $value): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->lte(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, $value);
    }

    public function applyNot(Specification $specification): void
    {
        $collector = $this->createCollector();
        $specification->applySpecification($collector);

        $this->constraints[] = sprintf('NOT (%s)', implode(' ', $collector->constraints));
        foreach ($collector->builder->getParameters() as $parameter => $value) {
            $this->builder->setParameter($parameter, $value);
        }
        $this->parameterCount = $collector->parameterCount;
    }

    public function applyOrderAsc(string $alias, string $property): void
    {
        $this->builder->addOrderBy(sprintf('%s.%s', $alias, $property), 'ASC');
    }

    public function applyOrderDesc(string $alias, string $property): void
    {
        $this->builder->addOrderBy(sprintf('%s.%s', $alias, $property), 'DESC');
    }

    public function applyStartsWith(string $alias, string $property, string $value): void
    {
        $parameter = $this->generateParameter($property);
        $this->constraints[] = $this->builder
            ->expr()
            ->like(sprintf('%s.%s', $alias, $property), ':' . $parameter);

        $this->builder->setParameter($parameter, $value . '%');
    }

    private function generateParameter(string $property): string
    {
        $this->parameterCount ++;

        return $property . '_' . $this->parameterCount;
    }

    private function buildQuery(Specification $specification): Result
    {
        $specification->applySpecification($this);
        if (count($this->constraints) > 0) {
            $this->builder->where(...$this->constraints);
        }

        if ($this->debug) {
            var_dump($this->builder->getSQL(), $this->builder->getParameters());
        }

        return $this->builder->executeQuery();
    }

    private function applyComposite(string $type, Specification ...$specifications): void
    {
        $collector = $this->createCollector();

        foreach ($specifications as $specification) {
            $specification->applySpecification($collector);
        }

        $constraints = $collector->constraints;
        if (count($constraints) > 0) {
            if ($type === 'AND') {
                $this->constraints[] = $this->builder->expr()->and(...$constraints);
            } else {
                $this->constraints[] = $this->builder->expr()->or(...$constraints);
            }
        }

        foreach ($collector->builder->getParameters() as $parameter => $value) {
            $this->builder->setParameter($parameter, $value);
        }
        $this->parameterCount = $collector->parameterCount;

        /**
         * @var string[] $orders
         */
        $orders = $collector->builder->getQueryPart('orderBy');
        foreach ($orders as $order) {
            $parts = explode(' ', $order);
            $this->builder->addOrderBy($parts[0], $parts[1]);
        }
    }
}
