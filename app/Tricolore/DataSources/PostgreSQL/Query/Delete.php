<?php
namespace Tricolore\DataSources\PostgreSQL\Query;

use Tricolore\DataSources\PostgreSQL\DatabaseFactory;
use Tricolore\Exception\DatabaseException;

class Delete
{
    /**
     * PDO instance
     * 
     * @var \PDO
     */
    private $pdo;

    /**
     * Collection
     * 
     * @var array
     */
    private $collection = [];

    /**
     * Database factory
     * 
     * @var Tricolore\DataSources\PostgreSQL\DatabaseFactory
     */
    private $factory;

    /**
     * Table prefix
     * 
     * @var string
     */
    private $table_prefix;

    /**
     * Construct
     * 
     * @param \PDO $pdo
     * @param Tricolore\DataSources\PostgreSQL\DatabaseFactory $factory
     * @param string $table_prefix
     * @return void
     */
    public function __construct(\PDO $pdo, DatabaseFactory $factory, $table_prefix)
    {
        $this->pdo = $pdo;
        $this->factory = $factory;
        $this->table_prefix = $table_prefix;
    }

    /**
     * Delete from
     * 
     * @param string $table_name 
     * @return Tricolore\DataSources\PostgreSQL\Query\Delete
     */
    public function deleteFrom($table_name)
    {
        $this->collection['delete_from'] = $this->table_prefix . $table_name;

        return $this;
    }

    /**
     * Where
     * 
     * @param string $where 
     * @param array $binding
     * @return Tricolore\DataSources\PostgreSQL\Query\Delete
     */
    public function where($where, array $binding = [])
    {
        $this->collection['delete_where'] = $where;
        $this->collection['where_binding'] = $binding;

        return $this;
    }

    /**
     * Execute
     * 
     * @throws Tricolore\Exception\DatabaseException
     * @return void
     */
    public function execute()
    {
        if (isset($this->collection['delete_from']) === false) {
            throw new DatabaseException('"From" in query is required. Add deleteFrom() method to your query builder.');
        }

        if (isset($this->collection['delete_where']) === false) {
            throw new DatabaseException('"Where" in query is required. Add where() method to your query builder.');
        }

        $query = sprintf('delete from %s where %s', $this->collection['delete_from'], $this->collection['delete_where']);

        $prepare = $this->pdo->prepare($query);

        $this->factory->binding($this->collection['where_binding'], $prepare);

        try {
            $prepare->execute();
        } catch (\PDOException $exception) {
            throw new DatabaseException($exception->getMessage());
        }
    }
}
