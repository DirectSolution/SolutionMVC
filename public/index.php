<?php
//Debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//Cors
//header("Access-Control-Allow-Origin: *");

// load application config (error reporting etc.)
use SolutionMvc\Config\Config,
    SolutionMvc\Core\Application;

// set a constant that holds the project's folder path, like "/var/www/".
// DIRECTORY_SEPARATOR adds a slash to the end of the path
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// set a constant that holds the project's "application" folder, like "/var/www/application".
define('APP', ROOT . 'Application' . DIRECTORY_SEPARATOR);
//Load composers Autoloader. If you get an error here don't forget to run "composer update" and if still getting errors try "composer dump-autoload"
require ROOT . 'vendor/autoload.php';

new Config();
new Application();

