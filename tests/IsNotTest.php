<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use Star\Component\Specification\AndX;
use Star\Component\Specification\Between;
use Star\Component\Specification\Contains;
use Star\Component\Specification\EqualsTo;
use Star\Component\Specification\IsNot;
use PHPUnit\Framework\TestCase;
use Star\Component\Specification\OrX;

final class IsNotTest extends TestCase
{
    public function test_it_should_inverse_the_given_specification(): void
    {
        $wrapped = EqualsTo::integerValue('alias', 'id', 1);

        self::assertCount(1, StarWarsCharacters::fetchAll($wrapped));
        self::assertCount(
            StarWarsCharacters::MAX_ROW_COUNT - 1,
            StarWarsCharacters::fetchAll(new IsNot($wrapped))
        );
    }

    public function test_it_should_inverse_complex_specification(): void
    {
        $wrapped = new OrX( // Skywalker family
            Contains::string('alias', 'name', 'leia'),
            Contains::string('alias', 'name', 'luke'),
            new AndX( // vader ony
                EqualsTo::booleanValue('alias', 'is_sith_lord', true),
                Between::integers('alias', 'total_kills', 30, 60)
            )
        );

        self::assertCount(3, $resultOfWrapped = StarWarsCharacters::fetchAll($wrapped));
        self::assertSame(StarWarsCharacters::ID_VADER, $resultOfWrapped->getValue(0, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LEIA, $resultOfWrapped->getValue(1, 'id')->toInteger());
        self::assertSame(StarWarsCharacters::ID_LUKE, $resultOfWrapped->getValue(2, 'id')->toInteger());

        self::assertCount(
            StarWarsCharacters::MAX_ROW_COUNT - 3,
            StarWarsCharacters::fetchAll(new IsNot($wrapped))
        );
    }
}
