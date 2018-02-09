<?php
namespace Model;

use MySQLi;

class Model
{
  private static $db;

  function __construct()
  {
    if (!isset(self::$db)) {
      $config = include __DIR__ . "/../config.php";

      self::$db = new mysqli($config['hostname'], $config['username'], $config['password'], $config['database']);
    }

    if (self::$db === false) {
      // TODO: handle internal error
    }
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
    return $uuid;
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

  function hex2base64url($uuid) {
    if (strpos($uuid, '-') !== false) {
      // The string contains '-', remove them
      $uuid = uuid2hex($uuid);
    }

    return $this->base64url_encode(pack('H*', $uuid));
  }

  function base64url2hex($uuid) {
    return bin2hex($this->base64url_decode($uuid));
  }

  private function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }

  private function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
  }
}
?>
