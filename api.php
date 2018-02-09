<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

use Controller\UsersController;
use Controller\OrganizationsController;
use Response\ErrorResponse;

// Register an autoloader for classes.
// The namespace will correspond to folder structure
spl_autoload_register(function ($class)
{
  $file = str_replace('\\', '/', $class) . '.php';
  if (file_exists($file)) {
    include $file;
  }
});

// Turn the request path into an array
$request = explode("/", $_GET['request']);

$request_body = json_decode(file_get_contents("php://input"));

$method = $_SERVER['REQUEST_METHOD'];

switch (array_shift($request)) {
  case 'users':
    $controller = new UsersController($request, $request_body);
    break;

  case 'organizations':
    $controller = new OrganizationsController($request, $request_body);
    break;

  default:
    ErrorResponse::invalidResource();
    break;
}

if (method_exists($controller, $method)) {
  $controller->$method();
} else {
  ErrorResponse::invalidMethod($controller->get_methods());
}
?>
