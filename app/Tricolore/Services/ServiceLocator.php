<?php
namespace Tricolore\Services;

use Tricolore\Application;
use Tricolore\Exception\ServicesException;
use Tricolore\Exception\AssetNotFound;
use Symfony\Component\Yaml\Yaml;

abstract class ServiceLocator
{
    /**
     * Get the service
     * 
     * @param string $key 
     * @param array $arguments
     * @param string $service_file
     * @throws Tricolore\Exception\ServicesException
     * @return mixed
     */
    final public function get($key, array $arguments = [], $service_file = null)
    {
        $service_map = $this->parseServicesMap($service_file);

        if (isset($service_map['service-locator'][$key]) === false) {
            throw new ServicesException(sprintf('Service "%s" not exists.', $key));
        }

        if (class_exists($service_map['service-locator'][$key]['class']) === false) {
            throw new ServicesException(sprintf('Class "%s" not exists.', $service_map['service-locator'][$key]['class']));
        }

        $service_load = $this->serviceClassLoad($service_map, $key);

        if (isset($service_map['service-locator'][$key]['function']) === false) {
            return $service_load;
        }

        if (method_exists($service_load, $service_map['service-locator'][$key]['function']) === false) {
            throw new ServicesException(sprintf('Method "%s" in class "%s" not exists.', 
                $service_map['service-locator'][$key]['function'], $service_map['service-locator'][$key]['class']));
        }

        if ($this->isStatic($service_map, $key) === true) {
            return $this->callStatic([$service_load, $service_map['service-locator'][$key]['function']], $arguments);
        }

        return $this->callDynamic([$service_load, $service_map['service-locator'][$key]['function']], $arguments);
    }

    /**
     * Call dymanic
     * 
     * @param callable $callback
     * @param array $arguments
     * @return mixed
     */
    final private function callDynamic(callable $callback, array $arguments)
    {
        return call_user_func_array($callback, $arguments);
    }

    /**
     * Call static
     * 
     * @param callable $callback
     * @param array $arguments
     * @return mixed
     */
    final private function callStatic(callable $callback, array $arguments)
    {
        return forward_static_call_array($callback, $arguments);
    }

    /**
     * Is static
     * 
     * @param array $service_map
     * @param string $key
     * @return bool
     */
    final private function isStatic(array $service_map, $key)
    {
        return isset($service_map['service-locator'][$key]['static']) && $service_map['service-locator'][$key]['static'] === true;
    }

    /**
     * Service class load
     * 
     * @param array $service_map
     * @param string $key 
     * @return mixed
     */
    final private function serviceClassLoad(array $service_map, $key)
    {
        if ($this->isStatic($service_map, $key) === true) {
            return $service_map['service-locator'][$key]['class'];
        }

        return new $service_map['service-locator'][$key]['class'];
    }

    /**
     * Parse YAML file to raw array
     * 
     * @param string $service_file
     * @throws Tricolore\Exception\AssetNotFound
     * @return array
     */
    final private function parseServicesMap($service_file)
    {
        if ($service_file === null) {
            $service_map = Yaml::parse(Application::createPath('app:Tricolore:Services:ServicesMap:Map.yml'));
        } else {
            if (file_exists($service_file) === false) {
                throw new AssetNotFound(sprintf('File: %s does not exists.', $service_file));
            }

            $service_map = Yaml::parse($service_file); 
        }

        return $service_map;
    }
}
