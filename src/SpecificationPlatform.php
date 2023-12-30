<?php declare(strict_types=1);

namespace Star\Component\Specification;

use DateTimeInterface;

interface SpecificationPlatform
{
    /**
     * @param Specification $first
     * @param Specification $second
     * @param Specification ...$others
     * @return void
     */
    public function applyAndX(Specification $first, Specification $second, Specification ...$others): void;

    /**
     * @param Specification $first
     * @param Specification $second
     * @param Specification ...$others
     * @return void
     */
    public function applyOrX(Specification $first, Specification $second, Specification ...$others): void;

    /**
     * @param string $alias
     * @param string $property
     * @param string $value
     * @return void
     */
    public function applyContains(string $alias, string $property, string $value): void;

    /**
     * @param string $alias
     * @param string $property
     * @param string $value
     * @return void
     */
    public function applyEndsWith(string $alias, string $property, string $value): void;

    /**
     * @param string $alias
     * @param string $property
     * @param string $value
     * @return void
     */
    public function applyEqualsString(string $alias, string $property, string $value): void;

    /**
     * @param string $alias
     * @param string $property
     * @param int $value
     * @return void
     */
    public function applyEqualsInteger(string $alias, string $property, int $value): void;

    /**
     * @param string $alias
     * @param string $property
     * @param float $value
     * @return void
     */
    public function applyEqualsFloat(string $alias, string $property, float $value): void;

    /**
     * @param string $alias
     * @param string $property
     * @param bool $value
     * @return void
     */
    public function applyEqualsBoolean(string $alias, string $property, bool $value): void;

    /**
     * @param string $alias
     * @param string $property
     * @param float $value
     * @return void
     */
    public function applyGreater(string $alias, string $property, float $value): void;

    /**
     * @param string $alias
     * @param string $property
     * @param DateTimeInterface $value
     * @return void
     */
    public function applyGreaterToDate(string $alias, string $property, DateTimeInterface $value): void;

    /**
     * @param string $alias
     * @param string $property
     * @param float $value
     * @return void
     */
    public function applyGreaterEquals(string $alias, string $property, float $value): void;

    /**
     * @param string $alias
     * @param string $property
     * @param DateTimeInterface $value
     * @return void
     */
    public function applyGreaterEqualsToDate(string $alias, string $property, DateTimeInterface $value): void;

    /**
     * @param string $alias
     * @param string $property
     * @param string ...$values
     * @return void
     */
    public function applyInStrings(string $alias, string $property, string ...$values): void;

    /**
     * @param string $alias
     * @param string $property
     * @param int ...$values
     * @return void
     */
    public function applyInIntegers(string $alias, string $property, int ...$values): void;

    /**
     * @param string $alias
     * @param string $property
     * @param float ...$values
     * @return void
     */
    public function applyInFloats(string $alias, string $property, float ...$values): void;

    /**
     * @param string $alias
     * @param string $property
     * @return void
     */
    public function applyIsNull(string $alias, string $property): void;

    /**
     * @param string $alias
     * @param string $property
     * @return void
     */
    public function applyIsEmpty(string $alias, string $property): void;

    /**
     * @param string $alias
     * @param string $property
     * @param float $value
     * @return void
     */
    public function applyLower(string $alias, string $property, float $value): void;

    /**
     * @param string $alias
     * @param string $property
     * @param DateTimeInterface $value
     * @return void
     */
    public function applyLowerToDate(string $alias, string $property, DateTimeInterface $value): void;

    /**
     * @param string $alias
     * @param string $property
     * @param float $value
     * @return void
     */
    public function applyLowerEquals(string $alias, string $property, float $value): void;

    /**
     * @param string $alias
     * @param string $property
     * @param DateTimeInterface $value
     * @return void
     */
    public function applyLowerEqualsToDate(string $alias, string $property, DateTimeInterface $value): void;

    /**
     * @param Specification $specification
     * @return void
     */
    public function applyNot(Specification $specification): void;

    /**
     * @param string $alias
     * @param string $property
     * @return void
     */
    public function applyOrderAsc(string $alias, string $property): void;

    /**
     * @param string $alias
     * @param string $property
     * @return void
     */
    public function applyOrderDesc(string $alias, string $property): void;

    /**
     * @param string $alias
     * @param string $property
     * @param string $value
     * @return void
     */
    public function applyStartsWith(string $alias, string $property, string $value): void;
}
