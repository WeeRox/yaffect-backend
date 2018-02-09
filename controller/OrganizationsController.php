<?php
namespace Controller;

use Model\Organization;
use Response;

class OrganizationsController extends Controller
{
  private $organization;

  function __construct($db, $request, $request_body)
  {
    $this->organization = new Organization($db);
    parent::__construct($request, $request_body);
  }

  function get()
  {
    $id = $this->checkForId();
    if ($id != null) {
      $json = $this->organization->getOrganizationById($id);
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
    $name = $this->getJsonValue("name");

    if ($name === null) {
      ErrorResponse::invalidRequest();
    } else {
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
  }

  function delete()
  {
    $id = $this->checkForId();
    if ($id != null) {
      $response = $this->organization->deleteOrganization($id);
      if ($response) {
        Response::response204();
        return;
      } else {
        Reponse::response500();
        return;
      }
    } else {
      ErrorResponse::invalidMethod(array("GET"));
    }
  }
}
?>
