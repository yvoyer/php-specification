# PHP Specification

![Build Status](https://github.com/yvoyer/php-specification/actions/workflows/tests.yml/badge.svg)

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
$row = $result->fetchOne(EqualsTo::booleanValue('alias', 'active', false));
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
echo $result->exists(EqualsTo::stringValue('alias', 'name', 'Joe')); // true
echo $result->exists(EqualsTo::stringValue('alias', 'name', 'Not found')); // false
```

## Supported specifications

### EqualsTo

Whether the property's value is matching exactly the provided value (`===`). 

Example:
```php
EqualsTo::stringValue('alias', 'name', 'Joe');
```

**Note**: Both values will be converted to a string in order to assert the equality.

### Between

Whether the property's numeric or date value is included between the left and right values.

Example:

```php
Between::integers('alias', 'age', 18, 40);
Between::dates('alias', 'published_at', new \DateTime('1900-01-01'), new \DateTime('2000-01-01 12:34:56'));
```

**Note**: The format `Y-m-d H:i:s` is the only supported format used for comparison.

### Contains

Whether the provided value is found at any position of the item's property (start, end, middle).

Example:

```php
Contains::string('alias', 'name', 'Joe');
```

### EndsWith

Whether the provided value is found at the end of the item's property.

Example:

```php
EndsWith::string('alias', 'name', 'Joe');
```

### Greater

Whether the property's numeric or date value is greater than the provided value.

Example:

```php
Greater::thanInteger('alias', 'age', 18);
Greater::thanDate('alias', 'born_at', new \DateTime('2000-01-01'));
```

### GreaterEquals

Whether the property's numeric or date value is greater or equal than the provided value.

Example:

```php
GreaterEquals::thanInteger('alias', 'age', 18);
GreaterEquals::thanDate('alias', 'born_at', new \DateTime('2000-01-01'));
```

### InArray

Whether the property's value is contained in the range of provided values.

Example:

```php
InArray::ofIntegers('alias', 'age', 18, 20, 34); // would return items with age 18, 20 or 34.
```

### IsEmpty

Whether the property's value is an empty value.

Example:

```php
new IsEmpty('alias', 'name');
```

**Note**: Zero, boolean false are considered empty.

### IsNot

Inverse the provided specification.

Example:

```php
new IsNot(Lower::thanInteger('alias', 'age', 18)); // would return items with age >= 19.
```

### IsNull

Whether the property's value is a null value.

Example:

```php
new IsNull('alias', 'age');
```

**Note**: Zero, boolean false and empty string are not considered null.

### Lower

Whether the property's numeric or date value is less than the provided value.

Example:

```php
Lower::thanInteger('alias', 'age', 18);
Lower::thanDate('alias', 'age', new \DateTime('2000-01-01'));
```

### LowerEquals

Whether the property's numeric or date value is less or equal than the provided value.

Example:

```php
LowerEquals::thanInteger('alias', 'age', 18);
LowerEquals::thanDate('alias', 'age', new \DateTime('2000-01-01'));
```

### StartsWith

Whether the provided value is found at the beginning of the item's property.

Example:

```php
StartsWith::string('alias', 'name', 'Joe');
```

### Composites (And / Or)

Using [AndX](src/AndX.php) and [OrX](src/OrX.php) you are able to configure a more complex specification to your need.

Example:

```php
// equivalent to "name = 'Joe' AND active = true"
new AndX(
    EqualsTo::stringValue('alias', 'name', 'Joe')
    EqualsTo::booleanValue('alias', 'active', true)
);

// equivalent to "name = 'Joe' OR active = true"
new OrX(
    EqualsTo::stringValue('alias', 'name', 'Joe')
    EqualsTo::booleanValue('alias', 'active', true)
);
```

## Ordering by properties

We can also order our specifications with 1 or more properties.

Example:

```php
new AndX(
    OrderBy::desc('alias', 'is_active'),
    OrderBy::asc('alias', 'age'),
    OrderBy::desc('alias', 'name'),
);
new OrX(
    OrderBy::desc('alias', 'is_active'),
    OrderBy::asc('alias', 'age'),
    OrderBy::desc('alias', 'name'),
);
```

## Supported frameworks

* [PHP in memory array](src/Platform/InMemoryPlatform.php) using [ResultSet](src/Result/ResultSet.php) implementations.
* [Doctrine DBAL](src/Platform/DoctrineDBALPlatform.php)
* Doctrine ORM (TODO)
