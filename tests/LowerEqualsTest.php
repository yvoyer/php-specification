<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use Star\Component\Specification\LowerEquals;
use PHPUnit\Framework\TestCase;

final class LowerEqualsTest extends TestCase
{
    public function test_when_property_less(): void
    {
        $rows = StarWarsCharacters::fetchAll(LowerEquals::thanInteger('alias', 'total_kills', 65));

        self::assertCount(3, $rows);
        self::assertSame(StarWarsCharacters::ID_VADER, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LEIA, $rows->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $rows->getValue(2, 'id')->toInteger());
    }

    public function test_when_property_equals(): void
    {
        $rows = StarWarsCharacters::fetchAll(LowerEquals::thanInteger('alias', 'total_kills', 66));

        self::assertCount(4, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_VADER, $rows->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LEIA, $rows->getValue(2, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $rows->getValue(3, 'id')->toInteger());
    }

    public function test_when_property_greater(): void
    {
        $rows = StarWarsCharacters::fetchAll(LowerEquals::thanInteger('alias', 'total_kills', 67));

        self::assertCount(4, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_VADER, $rows->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LEIA, $rows->getValue(2, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $rows->getValue(3, 'id')->toInteger());
    }
}
