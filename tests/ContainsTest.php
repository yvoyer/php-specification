<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use PHPUnit\Framework\TestCase;
use Star\Component\Specification\Contains;

final class ContainsTest extends TestCase
{
    public function test_it_should_support_like_matching_exact_string(): void
    {
        $datasource = StarWarsCharacters::createResultSet();
        $result = $datasource->fetchAll(Contains::caseSensitiveString('alias', 'faction', 'E')); // From Empire

        self::assertCount(2, $result);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $result->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_VADER, $result->getValue(1, 'id')->toInteger());
    }

    public function test_it_should_support_like_matching_case_insensitive_string(): void
    {
        $datasource = StarWarsCharacters::createResultSet();
        $result = $datasource->fetchAll(Contains::caseInsensitiveString('alias', 'faction', 'tH')); // The Empire / The Rebel Alliance

        self::assertCount(4, $result);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $result->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_VADER, $result->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LEIA, $result->getValue(2, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $result->getValue(3, 'id')->toInteger());
    }
}
