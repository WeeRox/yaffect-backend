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

use Response\ErrorResponse;
use Response\SuccessResponse;
use Model\User;

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

// The .htaccess have removed eventual first and last slash
$request = $_GET['request'];
$request_method = $_SERVER['REQUEST_METHOD'];
$request_body = json_decode(file_get_contents("php://input"));

if ($request_body === NULL) {
	$request_body = json_decode("{}");
}

// Define all endpoints
$endpoints = array(
	"/^users$/" => array(
		"GET" => function() {
			$users = new User();
			$users = $users->getAll();
			SuccessResponse::ok(json_encode($users));
		},
		"POST" => function() use ($request_body) {
			// TODO: Do input validation
			$user = new User();
			$user->create($request_body->name, $request_body->birthdate);
			SuccessResponse::created(json_encode($user), "/users/" . $user->getId());
		}
	),
	"/^users\/([A-Za-z0-9_-]+)$/" => array(
		"GET" => function($match) {
			$user = new User();
			$user->getById($match[0]);
			SuccessResponse::ok(json_encode($user));
		}
	),
	"/^posts$/" => array(
		"POST" => function() use ($request_body) {
			$post = new Post();
			$post->create();
			SuccessResponse::created(json_encode($post), "/posts/" . $post->getId());
		}
	)
);

$matched_endpoint = false;
foreach ($endpoints as $key => $value) {
	if (preg_match($key, $request, $match)) {
		$matched_endpoint = $value;
		break;
	}
}

if ($matched_endpoint !== false) {
	if (array_key_exists($request_method, $matched_endpoint)) {
		// Remove 'full match' element
		array_shift($match);
		$matched_endpoint[$request_method]($match);
	} else {
		ErrorResponse::invalidMethod(array_keys($matched_endpoint));
	}
} else {
	// No endpoint matching the request exists
	// TODO: Handle error
}
?>
