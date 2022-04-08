<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use Star\Component\Specification\IsEmpty;
use PHPUnit\Framework\TestCase;

final class IsEmptyTest extends TestCase
{
    public function test_it_should_return_item_considered_empty(): void
    {
        $result = StarWarsCharacters::fetchAll(new IsEmpty('alias', 'alias'));

        self::assertCount(5, $result);
        self::assertSame(StarWarsCharacters::ID_LEIA, $result->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $result->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_BOBA, $result->getValue(2, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $result->getValue(3, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_STORMTROOPER, $result->getValue(4, 'id')->toInteger());
    }

    public function test_boolean_should_not_be_considered_empty(): void
    {
        self::assertFalse(StarWarsCharacters::exists(new IsEmpty('alias', 'is_sith_lord')));
    }

    public function test_zero_should_not_be_considered_empty(): void
    {
        self::assertFalse(StarWarsCharacters::exists(new IsEmpty('alias', 'salary')));
    }
}
