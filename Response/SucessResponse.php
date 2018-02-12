<?php
namespace Response;

class SuccessResponse
{
  public static function ok($output)
  {
    http_response_code(200);
    header("Content-Type: application/json; charset=UTF-8");
    echo $output;
    exit;
  }

  public static function created($output, $location)
  {
    http_response_code(201);
    header("Location: " . $location);
    header("Content-Type: application/json; charset=UTF-8");

    echo $output;
    exit;
  }

  public static function deleted()
  {
    http_response_code(204);
    exit;
  }
}
?>
