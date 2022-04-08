<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use InvalidArgumentException;
use Star\Component\Specification\AndX;
use Star\Component\Specification\OrderBy;
use PHPUnit\Framework\TestCase;
use Star\Component\Specification\Result\ResultRow;
use Star\Component\Specification\SpecificationPlatform;
use function var_dump;

final class OrderByTest extends TestCase
{
    public function test_it_should_throw_exception_when_invalid_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The direction order "invalid" is not supported.');
        new OrderBy('alias', 'name', 'invalid');
    }

    public function test_it_should_be_case_insensitive(): void
    {
        $asc = new OrderBy('alias', 'name', 'AsC');
        $ascPlatform = $this->createMock(SpecificationPlatform::class);
        $ascPlatform
            ->expects(self::once())
            ->method('applyOrderAsc')
            ->with('alias', 'name');
        $asc->applySpecification($ascPlatform);

        $descPlatform = $this->createMock(SpecificationPlatform::class);
        $descPlatform
            ->expects(self::once())
            ->method('applyOrderDesc')
            ->with('alias', 'name');
        $desc = new OrderBy('alias', 'name', 'DeSc');
        $desc->applySpecification($descPlatform);
    }

    public function test_it_should_order_by_single_column_asc(): void
    {
        $result = StarWarsCharacters::fetchAll(OrderBy::asc('alias', 'name'));

        self::assertCount(StarWarsCharacters::MAX_ROW_COUNT, $result);
        self::assertSame(
            StarWarsCharacters::ID_VADER,
            $result->getValue(0, 'id')->toInteger()
        );
        self::assertSame(
            StarWarsCharacters::ID_STORMTROOPER,
            $result->getValue(StarWarsCharacters::MAX_ROW_COUNT - 1, 'id')->toInteger()
        );
    }

    public function test_it_should_order_by_single_column_desc(): void
    {
        $result = StarWarsCharacters::fetchAll(OrderBy::desc('alias', 'name'));

        self::assertCount(StarWarsCharacters::MAX_ROW_COUNT, $result);
        self::assertSame(
            StarWarsCharacters::ID_STORMTROOPER,
            $result->getValue(0, 'id')->toInteger()
        );
        self::assertSame(
            StarWarsCharacters::ID_VADER,
            $result->getValue(StarWarsCharacters::MAX_ROW_COUNT - 1, 'id')->toInteger()
        );
    }

    public function test_it_should_order_by_multiple_columns(): void
    {
        $result = StarWarsCharacters::fetchAll(
            new AndX(
                OrderBy::desc('alias', 'is_force_sensitive'),
                OrderBy::asc('alias', 'total_kills'),
                OrderBy::desc('alias', 'name'),
            ),
        );

        self::assertSame(
            StarWarsCharacters::ID_LEIA,
            $result->getValue(0, 'id')->toInteger()
        );
        self::assertSame(
            StarWarsCharacters::ID_LUKE,
            $result->getValue(1, 'id')->toInteger()
        );
        self::assertSame(
            StarWarsCharacters::ID_VADER,
            $result->getValue(2, 'id')->toInteger()
        );
        self::assertSame(
            StarWarsCharacters::ID_PALPATINE,
            $result->getValue(3, 'id')->toInteger()
        );
        self::assertSame(
            StarWarsCharacters::ID_STORMTROOPER,
            $result->getValue(4, 'id')->toInteger()
        );
        self::assertSame(
            StarWarsCharacters::ID_BOBA,
            $result->getValue(5, 'id')->toInteger()
        );
        self::assertSame(
            StarWarsCharacters::ID_JANGO,
            $result->getValue(6, 'id')->toInteger()
        );
    }
}
