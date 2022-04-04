<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use PHPUnit\Framework\TestCase;
use Star\Component\Specification\AndX;
use Star\Component\Specification\EqualsTo;
use Star\Component\Specification\OrX;
use Star\Component\Specification\Result\ArrayResult;

final class AndXTest extends TestCase
{
    public function test_it_should_apply_both_specification_to_collection(): void
    {
        $data = ArrayResult::fromRowsOfMixed(
            [
                'id' => 1,
                'active' => false,
                'age' => 21,
            ],
            [
                'id' => 2,
                'active' => true,
                'age' => 21,
            ],
            [
                'id' => 3,
                'active' => false,
                'age' => 18,
            ],
            [
                'id' => 4,
                'active' => true,
                'age' => 18,
            ],
        );
        $result = $data->fetchAll(
            new AndX(
                EqualsTo::fromBoolean('alias', 'active', true),
                EqualsTo::fromInteger('alias', 'age', 18)
            )
        );

        self::assertCount(1, $result);
        self::assertSame(4, $result->getValue(0, 'id')->toInteger());
    }

    public function test_it_should_support_recursive_and(): void
    {
        $data = ArrayResult::fromRowsOfMixed(
            [
                'id' => 6,
                'name' => 'Joe',
                'active' => true,
                'age' => 18,
            ],
            [
                'id' => 2,
                'name' => 'Jane',
                'active' => true,
                'age' => 18,
            ],
            [
                'id' => 3,
                'name' => 'Joe',
                'active' => false,
                'age' => 18,
            ],
            [
                'id' => 6,
                'name' => 'Joe',
                'active' => true,
                'age' => 18,
            ],
            [
                'id' => 5,
                'name' => 'Joe',
                'active' => true,
                'age' => 76,
            ],
            [
                'id' => 6,
                'name' => 'Joe',
                'active' => true,
                'age' => 18,
            ],
        );
        $result = $data->fetchAll(
            new AndX(
                EqualsTo::fromBoolean('alias', 'active', true), // exclude 3
                new AndX(
                    EqualsTo::fromInteger('alias', 'age', 18), // exclude 5
                    new AndX(
                        EqualsTo::fromString('alias', 'name', 'Joe'), // exclude 2
                        EqualsTo::fromInteger('alias', 'id', 6)
                    )
                )
            )
        );

        self::assertCount(3, $result);
        self::assertSame(6, $result->getValue(0, 'id')->toInteger());
        self::assertSame(6, $result->getValue(1, 'id')->toInteger());
        self::assertSame(6, $result->getValue(2, 'id')->toInteger());
    }
}
