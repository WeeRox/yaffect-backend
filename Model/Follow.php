<?php
namespace Model;

class Follow extends Model
{
	private $organization_id;

	public function create($user_id, $organization_id)
	{
		$user_id = $this->base64url2hex($user_id);
		$organization_id = $this->base64url2hex($organization_id);
		if (parent::$db->query("INSERT INTO follows VALUES (UNHEX('$user_id'), UNHEX('$organization_id'));")) {

		} else {
			// TODO: Database error
		}
	}

	public function getAll($user_id)
	{
		$user_id = $this->base64url2hex($user_id);
		if ($result = parent::$db->query("SELECT organization_id FROM follows WHERE user_id = UNHEX('$user_id');")) {
			$follows = array();
			while ($row = $result->fetch_assoc()) {
				$follows[] = $this->hex2base64url(bin2hex($row['organization_id']));
			}

			$result->close();
			return $follows;
		} else {
			// TODO: Database error
		}
	}
}
?>
