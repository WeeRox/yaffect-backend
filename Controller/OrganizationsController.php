<?php
namespace Controller;

use Model\Organization;
use Response\SuccessResponse;
use Response\ErrorResponse;

class OrganizationsController extends Controller
{
  private $organization;

  function __construct($request, $request_body)
  {
    $this->organization = new Organization();
    parent::__construct($request, $request_body);
  }

  function get_methods()
  {
    return array("GET", "POST", "DELETE");
  }

  function get()
  {
    if ($this->hasId()) {
      $json = $this->organization->getOrganizationById($this->getId());

      if ($json === null) {
        ErrorResponse::invalidResource();
      }

      if ($json === false) {
        ErrorResponse::serverError();
      }
    } else {
      //TODO: return 404 error code if there are no elements
      if (!($json = $this->organization->getOrganizations())) {
        ErrorResponse::serverError();
      }
    }
    SuccessResponse::ok($json);
  }

  function post()
  {
    // check if request body contains name parameter
    if (!property_exists($this->request_body, "name")) {
      ErrorResponse::invalidRequest();
    }

    $name = $this->request_body->name;

    if (!($id = $this->organization->createOrganization($name))) {
      ErrorResponse::serverError();
    }
    if (!($json = $this->organization->getOrganizationById($id))) {
      ErrorResponse::serverError();
    }
    $location = "\/organizations/" . $this->organization->hex2uuid($id); // The first slash has to be escaped, else it will be read as a regex.
    SuccessResponse::created($json, $location);
  }

  function delete()
  {
    if ($this->hasId()) {
      if ($this->organization->deleteOrganization($this->getId())) {
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
