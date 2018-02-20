<?php
$config = include 'config.php';

if ($config['debug']) {
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
} else {
  error_reporting(0);
  ini_set('display_errors', 'Off');
}

// Register an autoloader for classes.
// The namespace will correspond to folder structure
spl_autoload_register(function ($class)
{
  $file = str_replace('\\', '/', $class) . '.php';
  if (file_exists($file)) {
    include $file;
  }
});

use Controller\UsersController;
use Controller\OrganizationsController;
use Response\ErrorResponse;

// No authentication included
if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
  ErrorResponse::invalidClient();
}

$auth = explode(" ", $_SERVER['HTTP_AUTHORIZATION']);
// The request used an unsupported authentication method
if ($auth[0] !== "Bearer") {
  ErrorResponse::invalidClient();
}

$token = $auth[1];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $config['url_oauth2'] . '/introspect');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $config['client_id'] . ":" . $config['client_secret']);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("token" => "$token")));

curl_setopt($ch, CURLINFO_HEADER_OUT, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
  // TODO: Handle cURL error
}

$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response_code != 200) {
  // TODO: Handle error
}

$response = json_decode($response);

// The token isn't active
if ($response->active === false) {
  ErrorResponse::invalidToken();
}

// Turn the request path into an array
$request = explode("/", $_GET['request']);

$request_body = json_decode(file_get_contents("php://input"));

if ($request_body === NULL) {
  $request_body = json_decode("{}");
}

$request_method = $_SERVER['REQUEST_METHOD'];

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

if (method_exists($controller, $request_method)) {
  $controller->$request_method();
} else {
  ErrorResponse::invalidMethod($controller->get_methods());
}
?>
