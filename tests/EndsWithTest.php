<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use Star\Component\Specification\EndsWith;
use PHPUnit\Framework\TestCase;
use Star\Component\Specification\StartsWith;

final class EndsWithTest extends TestCase
{
    public function test_it_should_return_items_ending_with_case_sensitive_string(): void
    {
        $result = StarWarsCharacters::fetchAll(EndsWith::string('alias', 'name', 'Skywalker'));

        self::assertCount(2, $result);
        self::assertSame(StarWarsCharacters::ID_VADER, $result->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $result->getValue(1, 'id')->toInteger());
    }

    public function test_it_should_return_items_ending_with_case_insensitive_string(): void
    {
        $result = StarWarsCharacters::fetchAll(EndsWith::string('alias', 'name', 'FetT'));

        self::assertCount(2, $result);
        self::assertSame(StarWarsCharacters::ID_BOBA, $result->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $result->getValue(1, 'id')->toInteger());
    }
}
