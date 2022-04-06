# PHP Specification

Specification system for php. Provides adapter for some libraries.

## Installation

Using [Composer](https://getcomposer.org): `composer require star/php-specification`

## Usage

### With basic php array

#### fetchAll()

Fetch all items matching the specification

```php
$data = [
    [
        'id' => 1,
        'name' => 'Joe',
        'active' => false,
    ],
    [
        'id' => 2,
        'name' => 'Jane',
        'active' => true,
    ],
    [
        'id' => 3,
        'name' => 'Jack',
        'active' => true,
    ],
];
$result = ArrayResult::fromRowsOfMixed(...$data);
$items = $result->fetchAll(EqualsTo::fromBoolean('alias', 'active', true));
echo $items->count(); // 2
echo $items->getValue(0, 'name')->toInteger(); // Jane
echo $items->getValue(1, 'name')->toInteger(); // Jack
```

#### fetchOne()

Fetch single item matching the specification

**Note**: If the specification would result with more than one row, a [NotUniqueResult exception](src/Result/NotUniqueResult.php) will be thrown.

```php
$data = [
    [
        'id' => 1,
        'name' => 'Joe',
        'active' => false,
    ],
    [
        'id' => 2,
        'name' => 'Jane',
        'active' => true,
    ],
    [
        'id' => 3,
        'name' => 'Jack',
        'active' => true,
    ],
];
$result = ArrayResult::fromRowsOfMixed(...$data);
$row = $result->fetchOne(EqualsTo::fromBoolean('alias', 'active', false));
echo $row->count(); // 1
echo $row->getValue('name')->toInteger(); // Joe
```

#### exists()

Fetch whether an item matches the specification

```php
$data = [
    [
        'id' => 1,
        'name' => 'Joe',
        'active' => false,
    ],
    [
        'id' => 2,
        'name' => 'Jane',
        'active' => true,
    ],
    [
        'id' => 3,
        'name' => 'Jack',
        'active' => true,
    ],
];
$result = ArrayResult::fromRowsOfMixed(...$data);
echo $result->exists(EqualsTo::fromString('alias', 'name', 'Joe')); // true
echo $result->exists(EqualsTo::fromString('alias', 'name', 'Not found')); // false
```

## Supported specifications

### EqualsTo

The equals to specification will return items matching exactly the value `===`. 

Example: `EqualsTo::fromString('alias', 'name', 'Joe')`

**Note**: Both values will be converted to a string in order to assert the equality.

### StartsWith

Whether the provided value is found at the beginning of the item's property.

Example: `StartsWith::caseSensitiveString('alias', 'name', 'Joe')`

### EndsWith

Whether the provided value is found at the end of the item's property.

Example: `EndsWith::caseInsensitiveString('alias', 'name', 'Joe')`

### Contains

Whether the provided value is found at any position of the item's property (start, end, middle).

Example: `Contains::caseSensitiveString('alias', 'name', 'Joe')`

### Composites (And / Or)

Using [AndX](src/AndX.php) and [OrX](src/OrX.php) you are able to configure a more complex specification to your need.

Example:

```php
// equivalent to "name = 'Joe' AND active = true"
new AndX(
    EqualsTo::fromString('alias', 'name', 'Joe')
    EqualsTo::fromBoolean('alias', 'active', true)
);

// equivalent to "name = 'Joe' OR active = true"
new OrX(
    EqualsTo::fromString('alias', 'name', 'Joe')
    EqualsTo::fromBoolean('alias', 'active', true)
);
```

## Supported frameworks

* [PHP in memory array](src/Platform/InMemoryPlatform.php) using [ResultSet](src/Result/ResultSet.php) implementations.
* Doctrine DBAL (TODO)
* Doctrine ORM (TODO)
