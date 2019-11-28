<?php
namespace Tricolore\DataSources;

use Tricolore\Application;
use Tricolore\Config\Config;
use Tricolore\Exception\DatabaseException;

class DatabaseAdapter
{
    /**
     * Get database factory
     * 
     * @throws Tricolore\Exception\DatabaseException
     * @return Tricolore\DataSources\PostgreSQL\DatabaseFactory
     */
    public function getDatabaseFactory()
    {
        $database_config = Config::all('Database');

        $allowed_adapters = ['PostgreSQL'];

        if (in_array($database_config['adapter'], $allowed_adapters, true) === false) {
            throw new DatabaseException(
                sprintf('Adapter "%s" not found. Known adapters: %s', $database_config['adapter'], implode(', ', $allowed_adapters)));
        }

        $driver_class = __NAMESPACE__ . '\\' . $database_config['adapter'] . '\\DatabaseFactory';
        $driver_class = new $driver_class;

        $config_key = (Application::getInstance()->inTravis() === true) ? 'travis' : 'default';

        return $driver_class->connection($database_config[Application::getInstance()->getEnv()][$config_key]);
    }
}
