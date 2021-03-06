<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use Star\Component\Specification\StartsWith;
use PHPUnit\Framework\TestCase;

final class StartsWithTest extends TestCase
{
    public function test_it_should_return_items_starting_with_case_sensitive_string(): void
    {
        $result = StarWarsCharacters::fetchAll(StartsWith::string('alias', 'alias', 'Darth'));

        self::assertCount(2, $result);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $result->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_VADER, $result->getValue(1, 'id')->toInteger());
    }

    public function test_it_should_return_items_starting_with_case_insensitive_string(): void
    {
        $result = StarWarsCharacters::fetchAll(StartsWith::string('alias', 'name', 'l'));

        self::assertCount(2, $result);
        self::assertSame(StarWarsCharacters::ID_LEIA, $result->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $result->getValue(1, 'id')->toInteger());
    }
}
