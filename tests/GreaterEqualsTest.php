<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use DateTimeImmutable;
use Star\Component\Specification\GreaterEquals;
use PHPUnit\Framework\TestCase;

final class GreaterEqualsTest extends TestCase
{
    public function test_when_property_less(): void
    {
        $rows = StarWarsCharacters::fetchAll(GreaterEquals::thanInteger('alias', 'total_kills', 65));

        self::assertCount(3, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_BOBA, $rows->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $rows->getValue(2, 'id')->toInteger());
    }

    public function test_when_property_equals(): void
    {
        $rows = StarWarsCharacters::fetchAll(GreaterEquals::thanInteger('alias', 'total_kills', 66));

        self::assertCount(3, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_BOBA, $rows->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $rows->getValue(2, 'id')->toInteger());
    }

    public function test_when_property_greater(): void
    {
        $rows = StarWarsCharacters::fetchAll(GreaterEquals::thanInteger('alias', 'total_kills', 67));

        self::assertCount(2, $rows);
        self::assertSame(StarWarsCharacters::ID_BOBA, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $rows->getValue(1, 'id')->toInteger());
    }

    public function test_when_date_is_less_than_luke(): void
    {
        $rows = StarWarsCharacters::fetchAll(
            GreaterEquals::thanDate(
                'alias',
                'died_at',
                new DateTimeImmutable('2017-12-15 18:12:54')
            )
        );

        self::assertCount(3, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LEIA, $rows->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $rows->getValue(2, 'id')->toInteger());
    }

    public function test_when_date_is_equal_to_luke(): void
    {
        $rows = StarWarsCharacters::fetchAll(
            GreaterEquals::thanDate(
                'alias',
                'died_at',
                new DateTimeImmutable('2017-12-15 18:12:55')
            )
        );

        self::assertCount(3, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LEIA, $rows->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $rows->getValue(2, 'id')->toInteger());
    }

    public function test_when_date_is_greater_to_luke(): void
    {
        $rows = StarWarsCharacters::fetchAll(
            GreaterEquals::thanDate(
                'alias',
                'died_at',
                new DateTimeImmutable('2017-12-15 18:12:56')
            )
        );

        self::assertCount(2, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LEIA, $rows->getValue(1, 'id')->toInteger());
    }
}
