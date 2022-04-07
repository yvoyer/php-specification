<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use Star\Component\Specification\Between;
use PHPUnit\Framework\TestCase;

final class BetweenTest extends TestCase
{
    public function test_when_property_is_less_than_left(): void
    {
        $rows = StarWarsCharacters::fetchAll(
            Between::integers('alias', 'total_kills', 65, 70)
        );

        self::assertCount(2, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_BOBA, $rows->getValue(1, 'id')->toInteger());
    }

    public function test_when_property_is_less_than_right(): void
    {
        self::assertFalse(
            StarWarsCharacters::exists(Between::integers('alias', 'total_kills', 60, 65))
        );
    }

    public function test_when_property_equals_left(): void
    {
        $rows = StarWarsCharacters::fetchAll(
            Between::integers('alias', 'total_kills', 66, 70)
        );

        self::assertCount(2, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_BOBA, $rows->getValue(1, 'id')->toInteger());
    }

    public function test_when_property_equals_right(): void
    {
        $row = StarWarsCharacters::fetchOne(
            Between::integers('alias', 'total_kills', 60, 66)
        );

        self::assertFalse($row->isEmpty());
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $row->getValue('id')->toInteger());
    }

    public function test_when_property_greater_left(): void
    {
        $row = StarWarsCharacters::fetchOne(
            Between::integers('alias', 'total_kills', 67, 70)
        );

        self::assertFalse($row->isEmpty());
        self::assertSame(StarWarsCharacters::ID_BOBA, $row->getValue('id')->toInteger());
    }

    public function test_when_property_greater_right(): void
    {
        $rows = StarWarsCharacters::fetchAll(
            Between::integers('alias', 'total_kills', 60, 67)
        );

        self::assertCount(1, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
    }
}
