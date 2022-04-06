<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests;

use Star\Component\Specification\Datasource;
use Star\Component\Specification\Result\ArrayResult;

final class StarWarsCharacters
{
    const ID_PALPATINE = 1;
    const ID_VADER = 2;
    const ID_LEIA = 3;
    const ID_LUKE = 4;
    const ID_BOBA = 5;
    const ID_JANGO = 6;

    public static function createResultSet(): Datasource
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
                'total_kills' => 12,
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
}
