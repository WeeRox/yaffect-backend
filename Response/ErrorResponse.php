<?php
namespace Response;

class ErrorResponse
{
  private $response = array();

  private static function init()
  {
    header("Content-Type: application/json; charset=UTF-8");
  }

  // The request used a request method that isn't allowed
  public static function invalidMethod($allowed_methods)
  {
    self::init();
    http_response_code(405);

    $allow = "Allow: ";

    for ($i = 0; $i < count($allowed_methods); $i++) {
      if ($i != 0) {
        $allow .= ", ";
      }

      $allow .= $allowed_methods[$i];
    }

    header($allow);

    $response['error'] = 'invalid_method';
    echo json_encode($response);
  }

  // The resource wasn't found/doesn't exist
  public static function invalidResource()
  {
    self::init();
    http_response_code(404);

    $response['error'] = "invalid_resource";
    echo json_encode($response);
  }

  // The request was invalid
  public static function invalidRequest()
  {
    self::init();
    http_response_code(400);

    $response['error'] = 'invalid_request';
    echo json_encode($response);
  }
}
?>
