<?php declare(strict_types=1);

namespace Star\Component\Specification;

use Star\Component\Type\Value;

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
     * @param Value $value
     * @param bool $caseSensitive Whether to compare in a case-sensitive manner
     * @return void
     */
    public function applyContains(
        string $alias,
        string $property,
        Value $value,
        bool $caseSensitive
    ): void;

    /**
     * @param string $alias
     * @param string $property
     * @param Value $value
     * @param bool $caseSensitive Whether to compare in a case-sensitive manner
     * @return void
     */
    public function applyEndsWith(string $alias, string $property, Value $value, bool $caseSensitive): void;

    /**
     * @param string $alias
     * @param string $property
     * @param Value $value
     * @param bool $caseSensitive Whether to compare in a case-sensitive manner
     * @return void
     */
    public function applyStartsWith(string $alias, string $property, Value $value, bool $caseSensitive): void;

    /**
     * Equals to the same scalar|bool value
     *
     * @param string $alias
     * @param string $property
     * @param Value $value
     * @return void
     */
    public function applyEquals(string $alias, string $property, Value $value): void;
}
