<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use PHPUnit\Framework\TestCase;
use Star\Component\Specification\EqualsTo;
use Star\Component\Specification\OrX;
use Star\Component\Specification\Result\ArrayResult;

final class OrXTest extends TestCase
{
    public function test_it_should_return_result_using_simple_or(): void
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
            new OrX(
                EqualsTo::fromBoolean('alias', 'active', true),
                EqualsTo::fromInteger('alias', 'age', 18)
            )
        );

        self::assertCount(3, $result);
        self::assertSame(2, $result->getValue(0, 'id')->toInteger());
        self::assertSame(3, $result->getValue(1, 'id')->toInteger());
        self::assertSame(4, $result->getValue(2, 'id')->toInteger());
    }

    public function test_it_should_return_result_using_or_with_depth(): void
    {
        $data = ArrayResult::fromRowsOfMixed(
            [
                'id' => 1,
                'name' => 'Object 1',
                'age' => 42,
                'active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Object 2',
                'age' => 25,
                'active' => false,
            ],
            [
                'id' => 3,
                'name' => 'Object 3',
                'age' => 78,
                'active' => true,
            ],
            [
                'id' => 4,
                'name' => 'Object 4',
                'age' => 56,
                'active' => false,
            ],
            [
                'id' => 5,
                'name' => 'Object 5',
                'age' => 18,
                'active' => true,
            ],
        );
        $result = $data->fetchAll(
            new OrX(
                EqualsTo::fromString('alias', 'name', 'Object 2'),
                new OrX(
                    EqualsTo::fromString('alias', 'name', 'Object 1'),
                    new OrX(
                        EqualsTo::fromBoolean('alias', 'active', false),
                        EqualsTo::fromInteger('alias', 'age', 18),
                    )
                )
            )
        );
        self::assertCount(4, $result);
        self::assertSame(1, $result->getValue(0, 'id')->toInteger());
        self::assertSame(2, $result->getValue(1, 'id')->toInteger());
        self::assertSame(4, $result->getValue(2, 'id')->toInteger());
        self::assertSame(5, $result->getValue(3, 'id')->toInteger());
    }
}
