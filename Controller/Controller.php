<?php
namespace Controller;

class Controller
{
  protected $request;
  protected $request_body;

  function __construct($request, $request_body)
  {
    $this->request = $request;
    $this->request_body = $request_body;
  }

  function checkForId() {
    if (count($this->request) >= 1) {
      if (preg_match('/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i', $this->request[0])) {
        return $this->request[0];
      } else {
        return null;
      }
    } else {
      return null;
    }
  }

  function getJsonValue($name)
  {
    $object = $this->request_body;
    foreach (explode("->", $name) as $key => $value) {
      if (isset($object->$value)) {
        $object = $object->$value;
      } else {
        return null;
      }
    }

    return $object;
  }
}
?>
