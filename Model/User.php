<?php
namespace Model;

use JsonSerializable;

class User extends Model implements JsonSerializable
{
  private $id;
  private $name;
  private $birthdate;

  public function getById($id)
  {
    $id = $this->base64url2hex($id);
    if ($result = parent::$db->query("SELECT * FROM users WHERE user_id = UNHEX('$id');")) {
      // Check that a user with that id exists
      if ($result->num_rows != 1) {
        // TODO: Handle error
      }

      $row = $result->fetch_assoc();

      $this->id = $this->hex2base64url($id);
      $this->name = $row['name'];
      $this->birthdate = $row['birthdate'];
    } else {
      // TODO: Database error
    }
  }

  public function create($name, $birthdate)
  {
    $id = $this->uuid2hex($this->generateUUIDv4());
    if (parent::$db->query("INSERT INTO users VALUES (UNHEX('$id'), '$name', '$birthdate');")) {
      $this->id = $this->hex2base64url($id);
      $this->name = $name;
      $this->birthdate = $birthdate;
    } else {
      // TODO: Database error
    }
  }

  public function getId()
  {
    return $this->id;
  }

  public function jsonSerialize()
  {
    return array(
      "id" => $this->id,
      "name" => $this->name,
      "birthdate" => $this->birthdate
    );
  }
}
?>
