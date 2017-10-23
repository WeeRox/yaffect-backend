<?php
class Response
{

  function __construct()
  {
    # code...
  }

  static function response200($output='')
  {
    http_response_code(200);
    header("Content-Type: application/json");
    echo $output;
  }

  static function response201($output, $location) {
    http_response_code(201);
    header("Location: $location");
    header("Content-Type: application/json");
    echo $output;
  }

  static function response204()
  {
    http_response_code(204);
  }

  static function response400($message) {
    http_response_code(400);
    header("Content-Type: application/json");
    echo $message;
  }

  static function response404()
  {
    http_response_code(404);
  }

  static function response405($methods)
  {
    $allow = "Allow: ";

    for ($i = 0; $i < count($methods); $i++) {
      if ($i != 0) {
        $allow .= ", ";
      }
      $allow .= $methods[$i];
    }

    http_response_code(405);
    header($allow);
  }

  static function response500()
  {
    http_response_code(500);
  }
}
?>
