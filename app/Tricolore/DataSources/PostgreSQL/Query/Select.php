<?php
namespace Tricolore\DataSources\PostgreSQL\Query;

use Tricolore\DataSources\PostgreSQL\DatabaseFactory;
use Tricolore\Exception\DatabaseException;

class Select
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
     * Select
     * 
     * @param mixed $select 
     * @return Tricolore\DataSources\PostgreSQL\Query\Select
     */
    public function select($select)
    {
        $this->collection['select'] = $select;

        return $this;
    }

    /**
     * From
     * 
     * @param string $from 
     * @return Tricolore\DataSources\PostgreSQL\Query\Select
     */
    public function from($from)
    {
        $this->collection['from'] = $this->table_prefix . $from;

        return $this;
    }

    /**
     * Left join
     * 
     * @param string $left_join 
     * @param string $on
     * @return Tricolore\DataSources\PostgreSQL\Query\Select
     */
    public function leftJoin($left_join, $on)
    {
        $this->collection['left_join'] = $left_join;
        $this->collection['left_join_on'] = $on;

        return $this;
    }

    /**
     * Where
     * 
     * @param string $where
     * @param array $binding
     * @return Tricolore\DataSources\PostgreSQL\Query\Select
     */
    public function where($where, array $binding = [])
    {
        $this->collection['where'] = $where;
        $this->collection['where_binding'] = $binding;

        return $this;
    }

    /**
     * Group by
     * 
     * @param string $group_by
     * @return Tricolore\DataSources\PostgreSQL\Query\Select
     */
    public function groupBy($group_by)
    {
        $this->collection['group_by'] = $group_by;

        return $this;
    }

    /**
     * Order by
     * 
     * @param string $order_by
     * @param string $sorting
     * @return Tricolore\DataSources\PostgreSQL\Query\Select
     */
    public function orderBy($order_by, $sorting = 'ASC')
    {
        $this->collection['order_by'] = $order_by;

        $sorting = strtoupper($sorting);

        if (in_array($sorting, ['ASC', 'DESC'], true) === false) {
            $this->collection['order_by_sorting'] = 'ASC';
        } else {
            $this->collection['order_by_sorting'] = $sorting;
        }

        return $this;
    }

    /**
     * Max results
     * 
     * @param int $limit 
     * @return Tricolore\DataSources\PostgreSQL\Query\Select
     */
    public function maxResults($limit)
    {
        $this->collection['limit'] = $limit;

        return $this;
    }

    /**
     * Execute
     * 
     * @throws Tricolore\Exception\DatabaseException
     * @return array
     */
    public function execute()
    {
        if (isset($this->collection['select']) === false) {
            throw new DatabaseException('"Select" in query is required. Add select() method to your query builder.');
        }

        if (isset($this->collection['from']) === false) {
            throw new DatabaseException('"From" in query is required. Add from() method to your query builder.');
        }

        if (is_array($this->collection['select']) === true) {
            $this->collection['select'] = implode(',', $this->collection['select']);
        }

        if (is_array($this->collection['from']) === true) {
            $this->collection['from'] = implode(',', $this->collection['from']);
        }

        $query = sprintf('select %s ', $this->collection['select']);
        $query .= sprintf('from %s ', $this->collection['from']);

        if (isset($this->collection['left_join']) === true 
            && isset($this->collection['left_join_on']) === true
        ) {
            $query .= sprintf('left join %s on %s ', $this->collection['left_join'], $this->collection['left_join_on']);
        }

        if (isset($this->collection['where']) === true) {
            $query .= sprintf('where %s ', $this->collection['where']);
        }

        if (isset($this->collection['group_by']) === true) {
            $query .= sprintf('group by %s ', $this->collection['group_by']);
        }

        if (isset($this->collection['order_by']) === true) {
            if (is_array($this->collection['order_by']) === true) {
                $this->collection['order_by'] = implode(',', $this->collection['order_by']);
            }

            $query .= sprintf('order by %s %s ', $this->collection['order_by'], $this->collection['order_by_sorting']);
        }

        if (isset($this->collection['limit']) === true) {
            $query .= sprintf('limit %d', $this->collection['limit']);
        }

        $prepare = $this->pdo->prepare($query);

        if (isset($this->collection['where_binding']) === true) {
           $this->factory->binding($this->collection['where_binding'], $prepare); 
        }
        
        try {
            $prepare->execute();
        } catch (\PDOException $exception) {
            throw new DatabaseException($exception->getMessage());
        }
        
        return $prepare->fetchAll();
    }
}
