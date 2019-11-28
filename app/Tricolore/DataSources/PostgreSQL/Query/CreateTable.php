<?php
namespace Tricolore\DataSources\PostgreSQL\Query;

use Tricolore\DataSources\PostgreSQL\DatabaseFactory;
use Tricolore\Exception\DatabaseException;

class CreateTable
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
     * Name of table
     * 
     * @param string $name
     * @return Tricolore\DataSources\PostgreSQL\Query\CreateTable
     */
    public function name($name)
    {
        $this->collection['name'] = $this->table_prefix . $name;

        return $this;
    }

    /**
     * Columns
     * 
     * @param array $columns
     * @return Tricolore\DataSources\PostgreSQL\Query\CreateTable
     */
    public function columns(array $columns)
    {
        $this->collection['columns'] = $columns;

        return $this;
    }

    /**
     * Create if not exists
     *  
     * @return Tricolore\DataSources\PostgreSQL\Query\CreateTable
     */
    public function ifNotExists()
    {
        $this->collection['if_not_exists'] = true;

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
        if (isset($this->collection['name']) === false) {
            throw new DatabaseException('"Name" in query is required. Add name() method to your query builder.');
        }

        if (isset($this->collection['columns']) === false) {
            throw new DatabaseException('"Columns" in query is required. Add columns() method to your query builder.');
        }

        if (!count($this->collection['columns'])) {
            throw new DatabaseException('"Columns" array is empty.');
        }

        $query = 'create table ';

        if (isset($this->collection['if_not_exists']) && $this->collection['if_not_exists'] === true) {
            $query .= 'if not exists ';
        }

        $query .= sprintf('%s%s(', $this->table_prefix, $this->collection['name']);

        foreach ($this->collection['columns'] as $column_name => $column_type) {
            $columns[] = $column_name . ' ' . $column_type;
        }

        $query .= implode(',', $columns) . ');';

        $prepare = $this->pdo->prepare($query);

        try {
           $prepare->execute();
        } catch (\PDOException $exception) {
            throw new DatabaseException($exception->getMessage());
        }
    }
}