<?php
namespace Controller;

use Model\User;
use Response;
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
        return;
      }

      if ($json === false) {
        Response::response500();
        return;
      }
    } else {
      //TODO: return 404 error code if there are no elements
      if (!($json = $this->user->getUsers())) {
        Response::response500();
        return;
      }
      Response::response200($json);
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
      Response::response500();
      return;
    }

    if (!($json = $this->user->getUserById($id))) {
      Response::response500();
      return;
    }
    $location = "\/users/" . $this->user->hex2uuid($id); // The first slash has to be escaped, else it will be read as a regex.
    Response::response201($json, $location);
  }

  function delete()
  {
    if ($this->hasId()) {
      if ($this->user->deleteUser($this->getId())) {
        Response::response204();
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
