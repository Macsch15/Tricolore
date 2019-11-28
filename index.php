<?php
if(version_compare(phpversion(), '5.5.0', '<')) {
    die('Tricolore requires PHP 5.5 or higher.');
}

use Tricolore\Autoloader;
use Tricolore\Application;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'app' .
    DIRECTORY_SEPARATOR . 'Tricolore' .
    DIRECTORY_SEPARATOR . 'Autoloader.php';

require_once __DIR__ . DIRECTORY_SEPARATOR . '.helpers.php';

Autoloader::getInstance()->register();

Application::register([
    'directory' => __DIR__ . DIRECTORY_SEPARATOR,
    'environment' => 'dev',
    'version' => '0.1'
]);
