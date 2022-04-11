<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use Star\Component\Specification\InArray;
use PHPUnit\Framework\TestCase;

final class InArrayTest extends TestCase
{
    public function test_it_should_return_items_with_integer(): void
    {
        $result = StarWarsCharacters::fetchAll(InArray::ofIntegers('alias', 'id', 5, 6));

        self::assertCount(2, $result);
        self::assertSame(StarWarsCharacters::ID_BOBA, $result->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_JANGO, $result->getValue(1, 'id')->toInteger());
    }

    public function test_it_should_return_items_with_float(): void
    {
        $result = StarWarsCharacters::fetchAll(InArray::ofFloats('alias', 'salary', 10.25, 12.45));

        self::assertCount(2, $result);
        self::assertSame(StarWarsCharacters::ID_LEIA, $result->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $result->getValue(1, 'id')->toInteger());
    }

    public function test_it_should_return_items_with_string(): void
    {
        $result = StarWarsCharacters::fetchAll(InArray::ofStrings('alias', 'alias', 'Darth Vader', 'Darth Sidious'));

        self::assertCount(2, $result);
        self::assertSame(StarWarsCharacters::ID_PALPATINE, $result->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_VADER, $result->getValue(1, 'id')->toInteger());
    }
}
