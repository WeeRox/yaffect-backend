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

  static function response500()
  {
    http_response_code(500);
  }
}
?>
