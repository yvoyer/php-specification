<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use Star\Component\Specification\Greater;
use PHPUnit\Framework\TestCase;

final class GreaterTest extends TestCase
{
    public function test_when_property_less(): void
    {
        $rows = StarWarsCharacters::fetchAll(Greater::thanInteger('alias', 'total_kills', 65));

        self::assertCount(3, $rows);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_BOBA, $rows->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $rows->getValue(2, 'id')->toInteger());
    }

    public function test_when_property_equals(): void
    {
        $rows = StarWarsCharacters::fetchAll(Greater::thanInteger('alias', 'total_kills', 66));

        self::assertCount(2, $rows);
        self::assertSame(StarWarsCharacters::ID_BOBA, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $rows->getValue(1, 'id')->toInteger());
    }

    public function test_when_property_greater(): void
    {
        $rows = StarWarsCharacters::fetchAll(Greater::thanInteger('alias', 'total_kills', 67));

        self::assertCount(2, $rows);
        self::assertSame(StarWarsCharacters::ID_BOBA, $rows->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $rows->getValue(1, 'id')->toInteger());
    }
}
