<?php declare(strict_types=1);

namespace Star\Component\Specification\Tests\Platform;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\TestCase;
use Star\Component\Specification\AndX;
use Star\Component\Specification\Between;
use Star\Component\Specification\Contains;
use Star\Component\Specification\EndsWith;
use Star\Component\Specification\EqualsTo;
use Star\Component\Specification\Greater;
use Star\Component\Specification\GreaterEquals;
use Star\Component\Specification\InArray;
use Star\Component\Specification\IsEmpty;
use Star\Component\Specification\IsNot;
use Star\Component\Specification\IsNull;
use Star\Component\Specification\Lower;
use Star\Component\Specification\LowerEquals;
use Star\Component\Specification\OrderBy;
use Star\Component\Specification\OrX;
use Star\Component\Specification\Platform\DoctrineDBALPlatform;
use Star\Component\Specification\StartsWith;

final class DoctrineDBALTest extends TestCase
{
    const TABLE_AUTHOR = 'author';
    const TABLE_POST = 'post';

    const SHAKESPEAR = 1;
    const TOLKIEN = 2;
    const JK = 3;
    const ROBERT = 4;
    const KING = 5;

    const MAX_POST_COUNT = 14;

    private Connection $connection;

    protected function setUp(): void
    {
        $this->connection = DriverManager::getConnection(
            [
                'driver' => 'pdo_sqlite',
                'in_memory' => true,
            ]
        );
        $manager = $this->connection->createSchemaManager();
        $schema = $manager->createSchema();
        $posts = $schema->createTable(self::TABLE_POST);
        $posts->addColumn('id', Types::INTEGER);
        $posts->addColumn('title', Types::STRING);
        $posts->addColumn('published', Types::BOOLEAN);
        $posts->addColumn('version', Types::FLOAT);
        $posts->addColumn('archived_at', Types::DATETIME_IMMUTABLE, ['notNull' => false]);
        $posts->addColumn('author', Types::INTEGER, ['notNull' => false]);
        $posts->setPrimaryKey(['id']);
        $posts->addForeignKeyConstraint(self::TABLE_POST, ['author'], ['id']);

        $authors = $schema->createTable(self::TABLE_AUTHOR);
        $authors->addColumn('id', Types::INTEGER);
        $authors->addColumn('name', Types::STRING);
        $authors->addColumn('alias', Types::STRING);
        $authors->addColumn('active', Types::BOOLEAN);
        $authors->addColumn('registered_at', Types::DATETIME_IMMUTABLE);
        $authors->addColumn('archived_at', Types::DATETIME_IMMUTABLE, ['notNull' => false]);
        $authors->setPrimaryKey(['id']);

        foreach ($schema->getMigrateFromSql(new Schema(), $this->connection->getDatabasePlatform()) as $sql) {
            $this->connection->executeQuery($sql);
        }

        $this->createAuthor(self::SHAKESPEAR, 'William Shakespeare', '', false, '1999-12-31', '1600-01-01');
        $this->createAuthor(self::TOLKIEN, 'JRR Tolkien', 'tolkien', false, '2000-01-03', '1999-02-03');
        $this->createAuthor(self::JK, 'JK. Rowling', 'JK', true, '1999-12-30', null);
        $this->createAuthor(self::ROBERT, 'Robert Ludlum', '', true, '2000-01-02', null);
        $this->createAuthor(self::KING, 'Stephen King', 'SK', true, '2000-01-01', null);

        $this->createPost(1, 'Hamlet', false, 1.0, self::SHAKESPEAR, '1780-02-03');
        $this->createPost(2, 'Fellowship of the rings', false, 1.0, self::TOLKIEN, '1970-01-02');
        $this->createPost(3, 'Harry Potter 1', false, 1.0, self::JK, '1990-02-04');
        $this->createPost(4, 'Angels and Demons', true, 1.0, self::ROBERT, null);
        $this->createPost(5, 'It', true, 1.0, self::KING, null);
        $this->createPost(6, 'Hamlet', false, 1.1, self::SHAKESPEAR, '1870-01-01');
        $this->createPost(7, 'Fellowship of the rings', true, 1.1, self::TOLKIEN, null);
        $this->createPost(8, 'Harry Potter 2', true, 1.0, self::JK, null);
        $this->createPost(9, 'Hamlet', true, 2.0, self::SHAKESPEAR, null);
        $this->createPost(10, 'Two towers', false, 1.0, self::TOLKIEN, '1975-02-03');
        $this->createPost(11, 'Harry Potter 1', true, 2.0, self::JK, null);
        $this->createPost(12, 'Two towers', true, 2.0, self::TOLKIEN, null);
        $this->createPost(13, 'Harry Potter 3', true, 1.0, self::JK, null);
        $this->createPost(self::MAX_POST_COUNT, 'Return of the king', true, 1.0, self::TOLKIEN, null);
    }

    private function createAuthor(
        int $id,
        string $name,
        string $alias,
        bool $active,
        string $registeredAt,
        ?string $archivedAt
    ): void {
        $this->connection->insert(
            self::TABLE_AUTHOR,
            [
                'id' => $id,
                'name' => $name,
                'alias' => $alias,
                'active' => $active,
                'registered_at' => $registeredAt,
                'archived_at' => $archivedAt,
            ]
        );
    }

    private function createPost(
        int $id,
        string $title,
        bool $published,
        float $version,
        int $author,
        ?string $archivedAt
    ): void {
        $this->connection->insert(
            self::TABLE_POST,
            [
                'id' => $id,
                'title' => $title,
                'published' => $published,
                'version' => $version,
                'author' => $author,
                'archived_at' => $archivedAt,
            ]
        );
    }

    public function test_it_should_support_equals_with_true(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(EqualsTo::booleanValue('a', 'active', true));

        self::assertCount(3, $result);
        self::assertSame(self::JK, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::ROBERT, $result->getValue(1, 'id')->toInteger());
        self::assertSame(self::KING, $result->getValue(2, 'id')->toInteger());
    }

    public function test_it_should_support_equals_with_false(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(EqualsTo::booleanValue('a', 'active', false));

        self::assertCount(2, $result);
        self::assertSame(self::SHAKESPEAR, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::TOLKIEN, $result->getValue(1, 'id')->toInteger());
    }

    public function test_it_should_support_greater_equals(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(GreaterEquals::thanInteger('a', 'id', 3));

        self::assertCount(3, $result);
        self::assertSame(self::JK, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::ROBERT, $result->getValue(1, 'id')->toInteger());
        self::assertSame(self::KING, $result->getValue(2, 'id')->toInteger());
    }

    public function test_it_should_support_greater(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(Greater::thanFloat('a', 'id', 3));

        self::assertCount(2, $result);
        self::assertSame(self::ROBERT, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::KING, $result->getValue(1, 'id')->toInteger());
    }

    public function test_it_should_support_lower_equals(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(LowerEquals::thanInteger('a', 'id', 3));

        self::assertCount(3, $result);
        self::assertSame(self::SHAKESPEAR, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::TOLKIEN, $result->getValue(1, 'id')->toInteger());
        self::assertSame(self::JK, $result->getValue(2, 'id')->toInteger());
    }

    public function test_it_should_support_lower(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(Lower::thanInteger('a', 'id', 3));

        self::assertCount(2, $result);
        self::assertSame(self::SHAKESPEAR, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::TOLKIEN, $result->getValue(1, 'id')->toInteger());
    }

    /**
     * @depends test_it_should_support_and
     * @depends test_it_should_support_or
     */
    public function test_it_should_support_between(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(Between::integers('a', 'id', 2, 4));

        self::assertCount(3, $result);
        self::assertSame(self::TOLKIEN, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::JK, $result->getValue(1, 'id')->toInteger());
        self::assertSame(self::ROBERT, $result->getValue(2, 'id')->toInteger());
    }

    public function test_it_should_support_contains_string(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(Contains::string('a', 'name', 'R'));

        self::assertCount(4, $result);
        self::assertSame(self::SHAKESPEAR, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::TOLKIEN, $result->getValue(1, 'id')->toInteger());
        self::assertSame(self::JK, $result->getValue(2, 'id')->toInteger());
        self::assertSame(self::ROBERT, $result->getValue(3, 'id')->toInteger());
    }

    public function test_it_should_support_starts_with_string(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(StartsWith::string('a', 'name', 'j'));

        self::assertCount(2, $result);
        self::assertSame(self::TOLKIEN, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::JK, $result->getValue(1, 'id')->toInteger());
    }

    public function test_it_should_support_ends_with_string(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(EndsWith::string('a', 'name', 'INg'));

        self::assertCount(2, $result);
        self::assertSame(self::JK, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::KING, $result->getValue(1, 'id')->toInteger());
    }

    public function test_it_should_support_in_array(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(InArray::ofIntegers('a', 'id', 1, 3, 5));

        self::assertCount(3, $result);
        self::assertSame(self::SHAKESPEAR, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::JK, $result->getValue(1, 'id')->toInteger());
        self::assertSame(self::KING, $result->getValue(2, 'id')->toInteger());
    }

    public function test_it_should_support_empty(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);

        self::assertFalse($platform->exists(new IsEmpty('a', 'name')));
    }

    public function test_it_should_return_items_with_empty_string(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);

        $result = $platform->fetchAll(new IsEmpty('a', 'alias'));
        self::assertCount(2, $result);
        self::assertSame(self::SHAKESPEAR, $result->getValue(0, 'id') ->toInteger());
        self::assertSame(self::ROBERT, $result->getValue(1, 'id') ->toInteger());
    }

    public function test_it_should_consider_boolean_false_as_empty(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);

        $result = $platform->fetchAll(new IsEmpty('a', 'active'));
        self::assertCount(2, $result);
        self::assertSame(self::SHAKESPEAR, $result->getValue(0, 'id') ->toInteger());
        self::assertSame(self::TOLKIEN, $result->getValue(1, 'id') ->toInteger());
    }

    public function test_it_should_consider_null_values_empty(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);

        $result = $platform->fetchAll(new IsEmpty('a', 'archived_at'));
        self::assertCount(3, $result);
        self::assertSame(self::JK, $result->getValue(0, 'id') ->toInteger());
        self::assertSame(self::ROBERT, $result->getValue(1, 'id') ->toInteger());
        self::assertSame(self::KING, $result->getValue(2, 'id') ->toInteger());
    }

    public function test_it_should_support_is_null(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(new IsNull('a', 'archived_at'));

        self::assertCount(3, $result);
        self::assertSame(self::JK, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::ROBERT, $result->getValue(1, 'id')->toInteger());
        self::assertSame(self::KING, $result->getValue(2, 'id')->toInteger());
    }

    /**
     * @depends test_it_should_support_and
     * @depends test_it_should_support_or
     */
    public function test_it_should_support_not(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(new IsNot(new IsEmpty('a', 'active', true)));

        self::assertCount(3, $result);
        self::assertSame(self::JK, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::ROBERT, $result->getValue(1, 'id')->toInteger());
        self::assertSame(self::KING, $result->getValue(2, 'id')->toInteger());
    }

    public function test_it_should_support_single_order_by(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(OrderBy::desc('a', 'name'));

        self::assertCount(5, $result);
        self::assertSame(self::SHAKESPEAR, $result->getValue(0, 'id')->toInteger());
        self::assertSame(self::KING, $result->getValue(1, 'id')->toInteger());
        self::assertSame(self::ROBERT, $result->getValue(2, 'id')->toInteger());
        self::assertSame(self::TOLKIEN, $result->getValue(3, 'id')->toInteger());
        self::assertSame(self::JK, $result->getValue(4, 'id')->toInteger());
    }

    /**
     * @depends test_it_should_support_and
     * @depends test_it_should_support_or
     */
    public function test_it_should_support_multiple_order_by(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_AUTHOR, 'a');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(
            new AndX(
                OrderBy::desc('a', 'active'),
                new OrX(
                    OrderBy::desc('a', 'registered_at'),
                    OrderBy::desc('a', 'name'),
                ),
            )
        );

        self::assertCount(5, $result);
        self::assertSame(2, $result->getValue(0, 'id')->toInteger());
        self::assertSame(1, $result->getValue(1, 'id')->toInteger());
        self::assertSame(4, $result->getValue(2, 'id')->toInteger());
        self::assertSame(5, $result->getValue(3, 'id')->toInteger());
        self::assertSame(3, $result->getValue(4, 'id')->toInteger());
    }

    public function test_it_should_support_and(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_POST, 'p');

        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(
            new AndX(
                Greater::thanInteger('p', 'id', 6),
                new AndX(
                    EqualsTo::floatValue('p', 'version', 1), // exclude version 2
                    new AndX(
                        Contains::string('p', 'title', 'Potter'), // Exclude other non-Harry potter
                        StartsWith::string('p', 'title', 'Ha') // exclude Hamlet
                    )
                )
            )
        );

        self::assertCount(2, $result);
        self::assertSame(8, $result->getValue(0, 'id')->toInteger());
        self::assertSame(13, $result->getValue(1, 'id')->toInteger());
    }

    public function test_it_should_support_or(): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(self::TABLE_POST, 'p');
        $platform = new DoctrineDBALPlatform($qb);
        $result = $platform->fetchAll(
            new OrX(
                EqualsTo::integerValue('p', 'id', 4), // 1
                new OrX(
                    Greater::thanInteger('p', 'id', 13), // 2
                    new OrX(
                        Contains::string('p', 'title', 'Ham'), // 3
                        EqualsTo::booleanValue('p', 'published', false) // 4
                    )
                )
            )
        );

        self::assertCount(8, $result);
        self::assertSame(1, $result->getValue(0, 'id')->toInteger()); // 4
        self::assertSame(2, $result->getValue(1, 'id')->toInteger()); // 4
        self::assertSame(3, $result->getValue(2, 'id')->toInteger()); // 4
        self::assertSame(4, $result->getValue(3, 'id')->toInteger()); // 1
        self::assertSame(6, $result->getValue(4, 'id')->toInteger()); // 3
        self::assertSame(9, $result->getValue(5, 'id')->toInteger()); // 3
        self::assertSame(10, $result->getValue(6, 'id')->toInteger()); // 4
        self::assertSame(14, $result->getValue(7, 'id')->toInteger()); // 2
    }
}
