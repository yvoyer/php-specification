<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use Star\Component\Specification\Datasource;
use Star\Component\Specification\Result\ArrayResult;
use Star\Component\Specification\Result\ResultRow;
use Star\Component\Specification\Result\ResultSet;
use Star\Component\Specification\Specification;

final class StarWarsCharacters
{
    const MAX_ROW_COUNT = self::ID_STORMTROOPER;

    const ID_PALPATINE = 1;
    const ID_VADER = 2;
    const ID_LEIA = 3;
    const ID_LUKE = 4;
    const ID_BOBA = 5;
    const ID_JANGO = 6;
    const ID_STORMTROOPER = 7;

    private static function createResultSet(): Datasource
    {
        return ArrayResult::fromRowsOfMixed(
            [
                'id' => self::ID_PALPATINE,
                'name' => 'Emperor Palpatine',
                'alias' => 'Darth Sidious',
                'salary' => 66.66,
                'is_sith_lord' => true,
                'is_rebel' => false,
                'faction' => 'The Empire',
                'total_kills' => 66,
            ],
            [
                'id' => self::ID_VADER,
                'name' => 'Anakin Skywalker',
                'alias' => 'Darth Vader',
                'salary' => 50.25,
                'is_sith_lord' => true,
                'is_rebel' => false,
                'faction' => 'The Empire',
                'total_kills' => 34,
            ],
            [
                'id' => self::ID_LEIA,
                'name' => 'Leia Organa',
                'alias' => null,
                'salary' => 10.25,
                'is_sith_lord' => false,
                'is_rebel' => true,
                'faction' => 'The Rebel Alliance',
                'total_kills' => 12,
            ],
            [
                'id' => self::ID_LUKE,
                'name' => 'Luke Skywalker',
                'alias' => null,
                'salary' => 12.45,
                'is_sith_lord' => false,
                'is_rebel' => true,
                'faction' => 'The Rebel Alliance',
                'total_kills' => 22,
            ],
            [
                'id' => self::ID_BOBA,
                'name' => 'Boba Fett',
                'alias' => null,
                'salary' => 5000.0,
                'is_sith_lord' => false,
                'is_rebel' => false,
                'faction' => 'Crime Syndicate',
                'total_kills' => 69,
            ],
            [
                'id' => self::ID_JANGO,
                'name' => 'Jango Fett',
                'alias' => null,
                'salary' => 15000.0,
                'is_sith_lord' => false,
                'is_rebel' => false,
                'faction' => 'Crime Syndicate',
                'total_kills' => 72,
            ],
            [
                'id' => self::ID_STORMTROOPER,
                'name' => 'TK-421',
                'alias' => null,
                'salary' => 1,
                'is_sith_lord' => false,
                'is_rebel' => false,
                'faction' => 'The Empire',
                'total_kills' => 0,
            ],
        );
    }

    public static function fetchAll(Specification $specification): ResultSet
    {
        return self::createResultSet()->fetchAll($specification);
    }

    public static function fetchOne(Specification $specification): ResultRow
    {
        return self::createResultSet()->fetchOne($specification);
    }

    public static function exists(Specification $specification): bool
    {
        return self::createResultSet()->exists($specification);
    }
}
