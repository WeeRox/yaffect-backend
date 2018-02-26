<?php
namespace Model;

use JsonSerializable;

class User extends Model implements JsonSerializable
{
	private $id;
	private $name;
	private $birthdate;
	private $organization_id = null;

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

			$result->close();
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

	public function getAll()
	{
		if ($result = parent::$db->query("SELECT * FROM users;")) {
			$users = array();
			while ($row = $result->fetch_assoc()) {
				$user = new User();
				$user->setId($this->hex2base64url(bin2hex($row["user_id"])));
				$user->setName($row["name"]);
				$user->setBirthdate($row["birthdate"]);
				$users[] = $user;
			}

			$result->close();
			return $users;
		} else {
			// TODO: Database error
		}
	}

	public function getId()
	{
		return $this->id;
	}

	private function setId($id)
	{
		$this->id = $id;
	}

	private function setName($name)
	{
		$this->name = $name;
	}

	private function setBirthdate($birthdate)
	{
		$this->birthdate = $birthdate;
	}

	public function isAdmin()
	{
		$user_id = $this->base64url2hex($this->id);
		if ($result = parent::$db->query("SELECT * FROM organization_users WHERE user_id = UNHEX('$user_id');")) {
			if ($result->num_rows === 0) {
				return false;
			} else {
				$row = $result->fetch_assoc();
				$this->organization_id = $this->hex2base64url(bin2hex($row['organization_id']));
				return true;
			}
		} else {
			// TODO: Database error
		}
	}

	public function getOrganizationId()
	{
		// If isAdmin hasn't been called for some reason
		if ($this->organization_id === null) {
			$user_id = $this->base64url2hex($this->id);
			if ($result = parent::$db->query("SELECT * FROM organization_users WHERE user_id = '$user_id';")) {
				$row = $result->fetch_assoc();
				$this->organization_id = $this->hex2base64url(bin2hex($row['organization_id']));
			} else {
				// TODO: Database error
			}
		}
		return $this->organization_id;
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
