<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use Star\Component\Specification\Datasource;
use Star\Component\Specification\Result\ArrayResult;
use Star\Component\Specification\Result\ResultRow;
use Star\Component\Specification\Result\ResultSet;
use Star\Component\Specification\Specification;

final class StarWarsCharacters
{
    const ID_PALPATINE = 1;
    const ID_VADER = 2;
    const ID_LEIA = 3;
    const ID_LUKE = 4;
    const ID_BOBA = 5;
    const ID_JANGO = 6;

    private static function createResultSet(): Datasource
    {
        return ArrayResult::fromRowsOfMixed(
            [
                'id' => self::ID_PALPATINE,
                'name' => 'Emperor Palpatine',
                'alias' => 'Darth Sidius',
                'is_sith_lord' => true,
                'is_rebel' => false,
                'faction' => 'The Empire',
                'total_kills' => 66,
            ],
            [
                'id' => self::ID_VADER,
                'name' => 'Anakin Skywalker',
                'alias' => 'Darth Vader',
                'is_sith_lord' => true,
                'is_rebel' => false,
                'faction' => 'The Empire',
                'total_kills' => 34,
            ],
            [
                'id' => self::ID_LEIA,
                'name' => 'Leia Organa',
                'alias' => null,
                'is_sith_lord' => false,
                'is_rebel' => true,
                'faction' => 'The Rebel Alliance',
                'total_kills' => 12,
            ],
            [
                'id' => self::ID_LUKE,
                'name' => 'Luke Skywalker',
                'alias' => null,
                'is_sith_lord' => false,
                'is_rebel' => true,
                'faction' => 'The Rebel Alliance',
                'total_kills' => 22,
            ],
            [
                'id' => self::ID_BOBA,
                'name' => 'Boba Fett',
                'alias' => null,
                'is_sith_lord' => false,
                'is_rebel' => false,
                'faction' => 'Crime Syndicate',
                'total_kills' => 69,
            ],
            [
                'id' => self::ID_JANGO,
                'name' => 'Jango Fett',
                'alias' => null,
                'is_sith_lord' => false,
                'is_rebel' => false,
                'faction' => 'Crime Syndicate',
                'total_kills' => 72,
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
