<?php
namespace Controller;

use Model\Organization;
use Response;

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
        return;
      }

      if ($json === false) {
        Response::response500();
        return;
      }
    } else {
      //TODO: return 404 error code if there are no elements
      if (!($json = $this->organization->getOrganizations())) {
        Response::response500();
        return;
      }
    }
    Response::response200($json);
  }

  function post()
  {
    // check if request body contains name parameter
    if (!property_exists($this->request_body, "name")) {
      ErrorResponse::invalidRequest();
    }

    $name = $this->request_body->name;

    if (!($id = $this->organization->createOrganization($name))) {
      Response::response500();
      return;
    }
    if (!($json = $this->organization->getOrganizationById($id))) {
      Response::response500();
      return;
    }
    $location = "\/organizations/" . $this->organization->hex2uuid($id); // The first slash has to be escaped, else it will be read as a regex.
    Response::response201($json, $location);
  }

  function delete()
  {
    if ($this->hasId()) {
      if ($this->organization->deleteOrganization($this->getId())) {
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
