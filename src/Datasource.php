<?php declare(strict_types=1);

namespace Star\Component\Specification;

use Star\Component\Specification\Result\ResultRow;
use Star\Component\Specification\Result\ResultSet;

interface Datasource
{
    /**
     * Fetch a collection of row matching the specification from the datasource.
     *
     * @param Specification $specification
     * @return ResultSet
     */
    public function fetchAll(Specification $specification): ResultSet;

    /**
     * Fetch one row matching the specification from the datasource.
     *
     * @param Specification $specification
     * @return ResultRow
     */
    public function fetchOne(Specification $specification): ResultRow;

    /**
     * Return whether at least one item matching the specification exists in the datasource
     *
     * @param Specification $specification
     * @return bool
     */
    public function exists(Specification $specification): bool;
}
