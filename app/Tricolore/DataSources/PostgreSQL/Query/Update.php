<?php
namespace Tricolore\DataSources\PostgreSQL\Query;

use Tricolore\DataSources\PostgreSQL\DatabaseFactory;
use Tricolore\Exception\DatabaseException;

class Update
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
     * Table name
     * 
     * @param string $table_name
     * @return Tricolore\DataSources\PostgreSQL\Query\Update
     */
    public function table($table_name)
    {
        $this->collection['table_name'] = $this->table_prefix . $table_name;

        return $this;
    }

    /**
     * Set
     * 
     * @param string $set
     * @param array $binding
     * @return Tricolore\DataSources\PostgreSQL\Query\Update
     */
    public function set($set, array $binding = [])
    {
        $this->collection['set'] = $set;
        $this->collection['set_binding'] = $binding;

        return $this;
    }

    /**
     * Where
     * 
     * @param string $where
     * @param array $binding
     * @return Tricolore\DataSources\PostgreSQL\Query\Update
     */
    public function where($where, array $binding)
    {
        $this->collection['where'] = $where;
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
        if (isset($this->collection['table_name']) === false) {
            throw new DatabaseException('"Table" in query is required. Add table() method to your query builder.');
        }

        if (isset($this->collection['set']) === false) {
            throw new DatabaseException('"Set" in query is required. Add set() method to your query builder.');
        }

        $query = sprintf('update %s set %s ', $this->collection['table_name'], $this->collection['set']);

        if (isset($this->collection['where']) === true) {
            $query .= sprintf('where %s', $this->collection['where']);
        }

        $prepare = $this->pdo->prepare($query);
        
        if (isset($this->collection['where_binding']) === true) {
            $this->factory->binding($this->collection['where_binding'], $prepare);
        }

        if (isset($this->collection['set_binding']) === true) {
            $this->factory->binding($this->collection['set_binding'], $prepare);
        }

        try {
            $prepare->execute();
        } catch (\PDOException $exception) {
            throw new DatabaseException($exception->getMessage());
        }
    }
}
