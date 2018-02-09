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

  function hasId()
  {
    if (count($this->request) >= 1) {
      if (preg_match('/^[A-Za-z0-9_-]+$/', $this->request[0])) {
        return true;
      }
    }
    return false;
  }

  function getId() {
    return $this->request[0];
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
