<?php
namespace Model;

class Model
{
  protected $db;

  function __construct($db)
  {
    $this->db = $db;
  }

  function generateUUIDv4()
  {
    $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

      // 32 bits for "time_low"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),

      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,

      // 48 bits for "node"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    return $this->uuid2hex($uuid);
  }

  function uuid2hex($uuid) {
    $uuid = str_replace('-', '', $uuid);
    return $uuid;
  }

  function hex2uuid($uuid) {
    $uuid = substr_replace($uuid, '-', 20, 0);
    $uuid = substr_replace($uuid, '-', 16, 0);
    $uuid = substr_replace($uuid, '-', 12, 0);
    $uuid = substr_replace($uuid, '-', 8, 0);
    return $uuid;
  }
}
?>
