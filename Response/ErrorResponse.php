<?php
namespace Response;

class ErrorResponse
{
  // The request used a request method that isn't allowed
  public static function invalidMethod($allowed_methods)
  {
    header("Content-Type: application/json; charset=UTF-8");
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
    exit;
  }

  // The resource wasn't found/doesn't exist
  public static function invalidResource()
  {
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code(404);

    $response['error'] = "invalid_resource";
    echo json_encode($response);
    exit;
  }

  // The request was invalid
  public static function invalidRequest()
  {
    header("Content-Type: application/json; charset=UTF-8");
    http_response_code(400);

    $response['error'] = 'invalid_request';
    echo json_encode($response);
    exit;
  }
}
?>
