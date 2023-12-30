<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests\Result;

use Star\Component\Specification\Result\ArrayRow;
use PHPUnit\Framework\TestCase;
use Star\Component\Specification\Result\ColumnNotFound;

final class ArrayRowTest extends TestCase
{
    public function test_it_should_throw_exception_when_column_not_found(): void
    {
        $row = ArrayRow::fromMixedMap(['other' => 1, 'col' => 2]);

        $this->expectException(ColumnNotFound::class);
        $this->expectExceptionMessage('Column "not-found" was not found in result row. Available columns are' .
            ' "other, col".'
        );
        $row->getValue('not-found');
    }
}
