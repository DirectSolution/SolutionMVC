<?php
print "<pre>";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

error_reporting(E_ALL);
print "</pre>";
// TODO get rid of this and work with namespaces + composer's autoloader

// set a constant that holds the project's folder path, like "/var/www/".
// DIRECTORY_SEPARATOR adds a slash to the end of the path
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// set a constant that holds the project's "application" folder, like "/var/www/application".
define('APP', ROOT . 'Application' . DIRECTORY_SEPARATOR);

//Load composers Autoloader. If you get an error here don't forget to run "composer update" and if still getting errors try "composer dump-autoload"
require ROOT . 'vendor/autoload.php';

// load application config (error reporting etc.)
use SolutionMvc\Config\Config, PDO;
$config = new Config();

// load the application Core Controller



use SolutionMvc\Core\Application;
$app = new Application();
    
