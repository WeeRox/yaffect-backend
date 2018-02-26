<?php
namespace Model;

class Organization extends Model
{
	function createOrganization($name)
	{
		$id = $this->generateUUIDv4();
		if ($this->db->query("INSERT INTO organizations (id, name) VALUES (UNHEX('$id'), '$name');")) {
			return $this->hex2uuid($id);
		}
		return false;
	}

	function getOrganizationById($id)
	{
		$id = $this->uuid2hex($id);
		if ($result = $this->db->query("SELECT *, HEX(id) AS id FROM organizations WHERE id = UNHEX('$id');")) {
			if ($result->num_rows < 1) {
				return null;
			}

			$row = $result->fetch_assoc();
			$json = array(
				'id' => $this->hex2uuid($row['id']),
				'name' => $row['name']
			);

			$result->close();
			return json_encode($json);
		}
		return false;
	}

	function getOrganizations()
	{
		if ($result = $this->db->query("SELECT *, HEX(id) AS id FROM organizations;")) {
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
		if ($this->db->query("DELETE FROM organizations WHERE id = UNHEX('$id');")) {
			return true;
		}
		return false;
	}
}
?>
