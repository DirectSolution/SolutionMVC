<?php
//Debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//Cors
header("Access-Control-Allow-Origin: *");
session_start();
// load application config (error reporting etc.)
use SolutionMvc\Config\Config,
    SolutionMvc\Core\Application;

// set a constant that holds the project's folder path, like "/var/www/".
// DIRECTORY_SEPARATOR adds a slash to the end of the path
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// set a constant that holds the project's "application" folder, like "/var/www/application".
define('APP', ROOT . 'Application' . DIRECTORY_SEPARATOR);
define('LEG', $_SERVER['DOCUMENT_ROOT']."/");
define('HTTP_ROOT', "//".$_SERVER['HTTP_HOST']."/apps2/");
define('SERVER_ROOT', "//".$_SERVER['HTTP_HOST']);
define('FILESTORE', "/home/git/htmlp/html/doug/portal.solutionhost.co.uk/web/apps/Audit/Filestore/");
//print_r($_SERVER['HTTP_HOST']);


//Load composers Autoloader. If you get an error here don't forget to run "composer update" and if still getting errors try "composer dump-autoload"
require ROOT . 'vendor/autoload.php';



new Config();
new Application();

//new Twig_Loader_Filesystem(APP.'/View');
//new Twig_Environment($loader, array(
//    'cache' => APP.'/ViewCache',
//));

//require_once '/path/to/lib/Twig/Autoloader.php';
