<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use DateTimeImmutable;
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

    public function test_when_dates_are_lower(): void
    {
        $rows = StarWarsCharacters::fetchAll(
            Between::dates(
                'alias',
                'died_at',
                new DateTimeImmutable('2008-10-08 17:35:39'),
                new DateTimeImmutable('2019-12-20 14:23:18'),
            )
        );

        self::assertCount(2, $rows);
        self::assertSame(StarWarsCharacters::ID_LUKE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $rows->getValue(1, 'id')->toInteger());
    }

    public function test_when_dates_are_greater(): void
    {
        $rows = StarWarsCharacters::fetchAll(
            Between::dates(
                'alias',
                'died_at',
                new DateTimeImmutable('2008-10-08 17:35:40'),
                new DateTimeImmutable('2019-12-20 14:23:20'),
            )
        );

        self::assertCount(3, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $rows->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $rows->getValue(2, 'id')->toInteger());
    }

    public function test_when_dates_are_equal(): void
    {
        $rows = StarWarsCharacters::fetchAll(
            Between::dates(
                'alias',
                'died_at',
                new DateTimeImmutable('2008-10-08 17:35:40'),
                new DateTimeImmutable('2019-12-20 14:23:19'),
            )
        );

        self::assertCount(3, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $rows->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $rows->getValue(2, 'id')->toInteger());
    }

    public function test_when_dates_are_in_range(): void
    {
        $rows = StarWarsCharacters::fetchAll(
            Between::dates(
                'alias',
                'died_at',
                new DateTimeImmutable('2008-10-08 17:35:39'),
                new DateTimeImmutable('2019-12-20 14:23:20'),
            )
        );

        self::assertCount(3, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $rows->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $rows->getValue(2, 'id')->toInteger());
    }
}
