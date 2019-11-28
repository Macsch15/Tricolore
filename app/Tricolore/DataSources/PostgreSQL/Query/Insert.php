<?php
namespace Tricolore\DataSources\PostgreSQL\Query;

use Tricolore\DataSources\PostgreSQL\DatabaseFactory;
use Tricolore\Exception\DatabaseException;

class Insert
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
        $this->table_prefix = $table_prefix;
    }

    /**
     * Into
     * 
     * @param string $into
     * @return Tricolore\DataSources\PostgreSQL\Query\Insert
     */
    public function into($into)
    {
        $this->collection['into'] = $this->table_prefix . $into;

        return $this;
    }

    /**
     * Values
     * 
     * @param array $values_collection
     * @return Tricolore\DataSources\PostgreSQL\Query\Insert
     */
    public function values(array $values_collection)
    {
        $this->collection['values'] = $values_collection;

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
        if (isset($this->collection['into']) === false) {
            throw new DatabaseException('"Into" in query is required. Add into() method to your query builder.');
        }

        if (isset($this->collection['values']) === false) {
            throw new DatabaseException('"Values" in query is required. Add values() method to your query builder.');
        }

        if (!count($this->collection['values'])) {
            throw new DatabaseException('"Values" array is empty.');
        }

        $query = sprintf('insert into %s ', $this->collection['into']);

        $field = implode(', ', array_keys($this->collection['values']));
        $value = implode('\', \'', $this->collection['values']);

        $query .= sprintf('(%s) values (\'%s\')', $field, $value);

        $prepare = $this->pdo->prepare($query);

        try {
            $prepare->execute();
        } catch (\PDOException $exception) {
            throw new DatabaseException($exception->getMessage());
        }
    }
}