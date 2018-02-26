<?php
namespace Controller;

use Model\User;
use Response\SuccessResponse;
use Response\ErrorResponse;

class UsersController extends Controller
{

	private $user;

	function __construct($request, $request_body)
	{
		$this->user = new User();
		parent::__construct($request, $request_body);
	}

	function get_methods()
	{
		return array("GET", "POST", "DELETE");
	}

	function get()
	{
		if ($this->hasId()) {
			$json = $this->user->getUserById($this->getId());

			if ($json === null) {
				ErrorResponse::invalidResource();
			}

			if ($json === false) {
				ErrorResponse::serverError();
			}
		} else {
			//TODO: return 404 error code if there are no elements
			if (!($json = $this->user->getUsers())) {
				ErrorResponse::serverError();
			}
			SuccessResponse::ok($json);
		}
	}

	function post()
	{
		if (!property_exists($this->request_body, "name")) {
			ErrorResponse::invalidRequest();
		}

		if (!property_exists($this->request_body, "email")) {
			ErrorResponse::invalidRequest();
		}

		if (!property_exists($this->request_body, "password")) {
			ErrorResponse::invalidRequest();
		}

		if (!property_exists($this->request_body, "birthdate")) {
			ErrorResponse::invalidRequest();
		}

		$name = $this->request_body->name;
		$email = $this->request_body->email;
		$password = $this->request_body->password;
		$birthdate = $this->request_body->birthdate;

		//Use bcrypt to hash the password
		$password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

		//TODO: Check that we don't create another user that already exist

		if (!($id = $this->user->createUser($name, $email, $password, $birthdate))) {
			ErrorResponse::serverError();
		}

		if (!($json = $this->user->getUserById($id))) {
			ErrorResponse::serverError();
		}
		$location = "\/users/" . $this->user->hex2uuid($id); // The first slash has to be escaped, else it will be read as a regex.
		SuccessResponse::created($json, $location);
	}

	function delete()
	{
		if ($this->hasId()) {
			if ($this->user->deleteUser($this->getId())) {
				SuccessResponse::deleted();
			} else {
				// TODO: internal error
			}
		} else {
			// if no id was sent a delete operation cannot be performed
			ErrorResponse::invalidMethod(array("GET"));
		}
	}
}
?>
