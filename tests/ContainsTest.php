<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use PHPUnit\Framework\TestCase;
use Star\Component\Specification\Contains;

final class ContainsTest extends TestCase
{
    public function test_it_should_support_like_matching_exact_string(): void
    {
        $result = StarWarsCharacters::fetchAll(Contains::string('alias', 'faction', 'P')); // From Empire

        self::assertCount(3, $result);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $result->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_VADER, $result->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_STORMTROOPER, $result->getValue(2, 'id')->toInteger());
    }

    public function test_it_should_support_like_matching_case_insensitive_string(): void
    {
        $result = StarWarsCharacters::fetchAll(Contains::string('alias', 'faction', 'tH')); // The Empire / The Rebel Alliance

        self::assertCount(5, $result);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $result->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_VADER, $result->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LEIA, $result->getValue(2, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $result->getValue(3, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_STORMTROOPER, $result->getValue(4, 'id')->toInteger());
    }
}
