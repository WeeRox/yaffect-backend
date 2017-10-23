<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

use Controller\UsersController;
use Controller\OrganizationsController;
use Response;

// Register an autoloader for classes.
// The namespace will correspond to folder structure
spl_autoload_register(function ($class)
{
  $file = str_replace('\\', '/', $class) . '.php';
  if (file_exists($file)) {
    include $file;
  }
});

// Include config for MySQL server.
$config = include 'config.php';

// Create an connection to the MySQL server
$db = new mysqli($config['hostname'], $config['username'], $config['password'], $config['database']);

// Turn the request path into an array
$request = explode("/", $_GET['request']);

$request_body = json_decode(file_get_contents("php://input"));

$method = $_SERVER['REQUEST_METHOD'];

switch (array_shift($request)) {
  case 'users':
    $usersController = new UsersController($db, $request, $request_body);
    if (method_exists($usersController, $method)) {
      $usersController->$method();
    } else {
      Response::response405(array("GET", "POST"));
    }

    break;

  case 'organizations':
    $organizationsController = new OrganizationsController($db, $request, $request_body);
    if (method_exists($organizationsController, $method)) {
      $organizationsController->$method();
    } else {
      Response::response405(array("GET, POST, DELETE"));
    }
    break;

  default:
    # code...
    break;
}

$db->close();
?>
