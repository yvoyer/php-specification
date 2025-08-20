<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests\Result;

use InvalidArgumentException;
use Star\Component\Specification\EqualsTo;
use Star\Component\Specification\Result\ArrayResult;
use PHPUnit\Framework\TestCase;
use Star\Component\Specification\Result\NotUniqueResult;
use Star\Component\Type\Value;
use function iterator_to_array;

final class ArrayResultTest extends TestCase
{
    private function createResultSet(): ArrayResult
    {
        return ArrayResult::fromRowsOfMixed(
            [
                'int' => 1,
                'int-string' => 1,
                'text' => 'Name',
                'bool' => true,
                'bool-int' => 1,
                'bool-string' => '0',
                'nullable' => null,
                'float' => 12.34,
                'float-string' => '12.34',
            ]
        );
    }

    public function test_it_should_return_string_column(): void
    {
        $result = $this->createResultSet();

        self::assertSame(1, $result->getValue(0, 'int')->toInteger());
        self::assertSame(1, $result->getValue(0, 'int-string')->toInteger());
        self::assertSame('Name', $result->getValue(0, 'text')->toString());
        self::assertTrue($result->getValue(0, 'bool')->toBool());
        self::assertTrue($result->getValue(0, 'bool-int')->toBool());
        self::assertFalse($result->getValue(0, 'bool-string')->toBool());
        self::assertTrue($result->getValue(0, 'nullable')->isEmpty());
        self::assertSame(12.34, $result->getValue(0, 'float')->toFloat());
        self::assertSame(12.34, $result->getValue(0, 'float-string')->toFloat());
    }

    private function createFixtureForFiltering(): ArrayResult
    {
        return ArrayResult::fromRowsOfMixed(
            [
                'id' => 2,
                'name' => 'Joe',
                'is_active' => true,
            ],
            [
                'id' => 9,
                'name' => 'Andrew',
                'is_active' => false,
            ],
            [
                'id' => 5,
                'name' => 'John',
                'is_active' => true,
            ],
        );
    }

    public function test_it_should_support_equals_on_fetch_all(): void
    {
        $data = $this->createFixtureForFiltering();
        $result = $data->fetchAll(
            EqualsTo::integerValue('alias', 'id', 9)
        );

        self::assertCount(1, $result);
        self::assertSame('Andrew', $result->getValue(0, 'name')->toString());
    }

    public function test_it_should_support_equals_on_fetch_one(): void
    {
        $data = $this->createFixtureForFiltering();
        $row = $data->fetchOne(
            EqualsTo::integerValue('alias', 'id', 9)
        );

        self::assertFalse($row->isEmpty());
        self::assertSame('Andrew', $row->getValue('name')->toString());
    }

    public function test_it_should_support_returning_empty_result(): void
    {
        $data = $this->createFixtureForFiltering();
        $row = $data->fetchOne(
            EqualsTo::integerValue('alias', 'id', 42)
        );

        self::assertTrue($row->isEmpty());
        self::assertTrue($row->getValue('name')->isEmpty());
    }

    public function test_it_should_support_equals_on_exists(): void
    {
        $data = $this->createFixtureForFiltering();

        self::assertTrue(
            $data->exists(EqualsTo::integerValue('alias', 'id', 9))
        );
        self::assertFalse(
            $data->exists(EqualsTo::integerValue('alias', 'id', 42))
        );
    }

    public function test_it_should_not_allow_row_with_invalid_values(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Value of type "array" is not supported yet.'
        );
        ArrayResult::fromRowsOfMixed(
            [
                [],
            ]
        );
    }

    public function test_it_should_not_allow_row_with_invalid_indexes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Row must be given a string index as column identifier, got "123".'
        );
        ArrayResult::fromRowsOfMixed(
            [
                123 => $this->createMock(Value::class),
            ]
        );
    }

    public function test_it_should_return_empty_row_when_requesting_not_found_row(): void
    {
        $result = ArrayResult::fromRows();
        self::assertTrue($result->isEmpty());
        self::assertTrue($result->getRow(42)->isEmpty());
    }

    public function test_it_should_return_empty_value_when_column_not_found(): void
    {
        $result = ArrayResult::fromRowsOfMixed();
        self::assertTrue($result->getValue(0, 'not-found')->isEmpty());
    }

    public function test_it_should_throw_exception_when_more_than_one_row_returned_for_fetch_one(): void
    {
        $result = $this->createFixtureForFiltering();

        $this->expectException(NotUniqueResult::class);
        $this->expectExceptionMessage('Query was expected to return 0-1 row, "2" rows returned.');
        $result->fetchOne(EqualsTo::booleanValue('alias', 'is_active', true));
    }

    public function test_it_should_be_iterator(): void
    {
        $result = ArrayResult::fromRowsOfMixed(
            [
                'id' => 1,
            ],
            [
                'id' => 2,
            ],
            [
                'id' => 3,
            ],
        );
        $rows = iterator_to_array($result);
        self::assertCount(3, $rows);
    }
}
