<?php
namespace Tricolore\DataSources\PostgreSQL;

use Tricolore\Exception\DatabaseException;
use Tricolore\Exception\InvalidArgumentException;

class DatabaseFactory
{
    /**
     * PDO instance
     * 
     * @var \PDO
     */
    private $pdo;

    /**
     * Table prefix
     * 
     * @var string
     */
    private $table_prefix;

    /**
     * Number of queries
     * 
     * @var int
     */
    private static $queries;

    /**
     * Connection
     * 
     * @param array $config
     * @throws Tricolore\Exception\DatabaseException
     * @return Tricolore\DataSources\PostgreSQL\DatabaseFactory
     */
    public function connection(array $config)
    {
        if (isset($config['server']) === false) {
            $config['server'] = 'localhost';
        }

        if (isset($config['port']) === false) {
            $config['port'] = 5432;
        }

        if (isset($config['database_name']) === false) {
            $config['database_name'] = 'tricolore';
        }

        if (isset($config['username']) === false) {
            $config['username'] = 'root';
        }

        if (isset($config['password']) === false) {
            $config['password'] = null;
        }

        if (isset($config['table_prefix']) === false) {
            $config['table_prefix'] = null;
        }

        $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s', $config['server'], $config['port'], $config['database_name']);

        try {
            $this->pdo = new \PDO($dsn, $config['username'], $config['password']);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);


            $this->table_prefix = $config['table_prefix'];
        } catch (\PDOException $exception) {
            throw new DatabaseException($exception->getMessage());
        }

        return $this;
    }

    /**
     * Build query
     * 
     * @param string $type
     * @throws Tricolore\Exception\InvalidArgumentException
     * @return object
     */
    public function buildQuery($type)
    {
        self::$queries++;

        if (strpos($type, '_') !== false) {
            $type = underscoreToStudlyCaps($type);
        } else {
            $type = ucfirst($type);
        }

        $allowed_types = ['Select', 'Delete', 'Update', 'Insert', 'CreateTable'];

        if (in_array($type, $allowed_types, true) === false) {
            throw new InvalidArgumentException(
                sprintf('Type "%s" is not allowed. Known types: %s', $type, implode(', ', $allowed_types)));
        }

        $class_type = __NAMESPACE__ . '\\Query\\' . $type;

        return new $class_type($this->pdo, $this, $this->table_prefix);
    }

    /**
     * Binding
     * 
     * @param array $binding_container
     * @param \PDOStatement $prepare
     * @throws Tricolore\Exception\DatabaseException 
     * @return mixed
     */
    public function binding(array $binding_container, \PDOStatement $prepare)
    {
        if (!count($binding_container)) {
            return false;
        }

        if ($prepare->queryString == null) {
            return false;
        }

        foreach ($binding_container as $key => $binding) {
            if (isset($binding['value']) === false) {
                throw new DatabaseException('Missing "value" in parameters.');
            }

            $allowed_types = ['BOOL', 'NULL', 'INT', 'STR', 'LOB', 'STMT'];

            if (isset($binding['type']) === false) {
                $binding['type'] = 'STR';
            }

            $binding['type'] = strtoupper($binding['type']);

            if (in_array($binding['type'], $allowed_types, true) === false) {
                throw new DatabaseException(
                    sprintf('Unknown data type "%s". Known types: %s', $binding['type'], implode(', ', $allowed_types)));
            }

            $prepare->bindValue($key, $binding['value'], constant('PDO::PARAM_' . $binding['type']));
        }
    }

    /**
     * Execute query
     * 
     * @param $query 
     * @return int
     */
    public function exec($query)
    {
        self::$queries++;

        return $this->pdo->exec($query);
    }

    /**
     * Drop table
     * 
     * @param string|array $table_name 
     * @return int
     */
    public function dropTable($table_name)
    {
        if (is_array($table_name) === true && count($table_name)) {
            $table_name = implode(',', $table_name);
        }

        return $this->exec(sprintf('drop table if exists %s%s', $this->table_prefix, $table_name));
    }

    /**
     * Drop field
     * 
     * @param string $from_table
     * @param string|array $field_name
     * @return int
     */
    public function dropField($from_table, $field_name)
    {
        if (is_array($field_name) === true && count($field_name)) {
            foreach ($field_name as $field) {
                $this->exec(sprintf('alter table %s%s drop column if exists %s', $this->table_prefix, $from_table, $field));
            }

            return count($field_name);
        }

        return $this->exec(sprintf('alter table %s%s drop column if exists %s', $this->table_prefix, $from_table, $field_name));
    }

    /**
     * Field exists
     * 
     * @param string $field_name
     * @param string $in_table
     * @return bool
     */
    public function fieldExists($field_name, $in_table)
    {
        $query = $this->buildQuery('select')
        ->select('column_name')
        ->from('information_schema.columns')
        ->where('table_name=? and column_name=?', [
            1 => [
                'value' => $in_table
            ],

            2 => [
                'value' => $field_name
            ]
        ])
        ->execute();

        return count($query) ? true : false;
    }

    /**
     * Table exists
     * 
     * @param $table_name 
     * @return bool
     */
    public function tableExists($table_name)
    {
        $query = $this->buildQuery('select')
        ->select('*')
        ->from('pg_tables')
        ->where('schemaname = ?', [
            1 => [
                'value' => 'public'
            ]
        ])
        ->execute();

        if (count($query)) {
            foreach ($query as $key => $schema) {
                if ($schema['tablename'] === $table_name) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get all tables from database
     *  
     * @return array
     */
    public function getAllTables()
    {
        $query = $this->buildQuery('select')
        ->select('*')
        ->from('pg_tables')
        ->where('schemaname = ?', [
            1 => [
                'value' => 'public'
            ]
        ])
        ->execute();

        $tables = [];

        if (count($query)) {
            foreach ($query as $table) {
                $tables[] = $table['tablename'];
            }
        }

        return $tables;
    }

    /**
     * Database exists
     *
     * @param string $database_name
     * @return bool
     */
    public function databaseExists($database_name)
    {
        $query = $this->buildQuery('select')
        ->select('*')
        ->from('pg_catalog.pg_database')
        ->where('datname = ?', [
            1 => [
                'value' => $database_name
            ]
        ])
        ->execute();

        if (count($query)) {
            return true;
        }

        return false;
    }

    /**
     * Create database
     *
     * @param string $database_name
     * @throws Tricolore\Exception\InvalidArgumentException
     * @return int
     */
    public function createDatabase($database_name)
    {
        if (preg_match('/[^A-Za-z0-9_-]/', $database_name)) {
            throw new InvalidArgumentException('Database name contains prohibited characters.');
        }

        return $this->exec(sprintf('create database %s', $database_name));
    }

    /**
     * Get number of queries
     * 
     * @return int
     */
    public function getQueriesNumber()
    {
        return self::$queries;
    }
}
