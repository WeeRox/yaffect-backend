<?php
namespace Model;

class User extends Model
{
  function createUser($name, $email, $password, $birthdate)
  {
    $id = $this->generateUUIDv4();
    if ($this->db->query("INSERT INTO users (id, name, email, password, birthdate) VALUES (UNHEX('$id'), '$name', '$email', '$password', '$birthdate');")) {
      return $this->hex2uuid($id);
    }
    return false;
  }

  function getUserById($id)
  {
    $id = $this->uuid2hex($id);
    if ($result = $this->db->query("SELECT *, HEX(id) AS id FROM users WHERE id = UNHEX('$id');")) {
      if ($result->num_rows < 1) {
        return null;
      }

      $row = $result->fetch_assoc();
      $json = array(
        'id' => $this->hex2uuid($row['id']),
        'name' => $row['name'],
        'email' => $row['email'],
        'birthdate' => $row['birthdate']
      );

      $result->close();
      return json_encode($json);
    }
    return false;
  }

  function getUsers()
  {
    if ($result = $this->db->query("SELECT *, HEX(id) AS id FROM users;")) {
      $rows = array();
      while ($row = $result->fetch_assoc()) {
        $row['id'] = $this->hex2uuid($row['id']);
        $rows[] = $row;
      }

      $result->close();
      return json_encode($rows);
    }
    return false;
  }

  function deleteOrganization($id)
  {
    $id = $this->uuid2hex($id);
    if ($this->db->query("DELETE FROM users WHERE id = UNHEX('$id');")) {
      return true;
    }
    return false;
  }
}
?>
