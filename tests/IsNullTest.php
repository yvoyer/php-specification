<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use Star\Component\Specification\IsNull;
use PHPUnit\Framework\TestCase;

final class IsNullTest extends TestCase
{
    public function test_is_should_return_items_with_null_value(): void
    {
        $result = StarWarsCharacters::fetchAll(new IsNull('alias', 'alias'));

        self::assertCount(5, $result);
        self::assertSame(StarWarsCharacters::ID_LEIA, $result->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $result->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_BOBA, $result->getValue(2, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $result->getValue(3, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_STORMTROOPER, $result->getValue(4, 'id')->toInteger());
    }

    public function test_zero_should_not_be_considered_null(): void
    {
        self::assertFalse(StarWarsCharacters::exists(new IsNull('alias', 'salary')));
    }

    public function test_false_should_not_be_considered_null(): void
    {
        self::assertFalse(StarWarsCharacters::exists(new IsNull('alias', 'is_rebel')));
    }
}
