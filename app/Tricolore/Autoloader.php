<?php
namespace Tricolore;

class Autoloader
{
    /**
     * Loaded classes
     * 
     * @var array
     */
    private static $loadedClasses  = [];

    /**
     * Autoloader instance
     *
     * @return Tricolore\Autoloader
     */
    public static function getInstance()
    {
        return new static();
    }

    /**
     * Register autoload
     *
     * @return void
     */
    public function register()
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * Load class
     *
     * @param string $class
     * @return void
     */
    public function loadClass($class)
    {
        if (startsWith('Tricolore', $class, 9) === true) {
            $directory = 'app' . DIRECTORY_SEPARATOR;
        } else {
            $directory = 'lib' . DIRECTORY_SEPARATOR;
        }

        $base_dir = substr(__DIR__, 0, strripos(__DIR__, DIRECTORY_SEPARATOR) - 3);
        $class = str_replace(['_', '\\'], DIRECTORY_SEPARATOR, $class);

        self::$loadedClasses[$base_dir . $directory . $class] = $class;

        $this->requireFile($base_dir . $directory . $class . '.php');
    }

    /**
     * Loaded classess accessor
     * 
     * @return array
     */
    public static function getLoadedClasses()
    {
        return self::$loadedClasses;
    }

    /**
     * Require from the file system.
     * 
     * @param string $file
     * @return bool
     */
    private function requireFile($file)
    {
        if (is_file($file) === true && file_exists($file) === true) {
            require_once $file;

            return true;
        }

        return false;
    }
}
