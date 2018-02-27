<?php
namespace Model;

use JsonSerializable;

class ClassName extends AnswerPost implements JsonSerializable
{
	private $alternatives = array();

	public function __construct()
	{
		parent::__construct();
		$this->answer_type = "singlechoice";
	}

	public function create($question, $organization_id, $alternatives)
	{
		$id = $this->uuid2hex($this->generateUUIDv4());
		$organization_id = $this->base64url2hex($organization_id);

		// Create the query which will add the alternatives to the database
		$alternatives_query = "";
		foreach ($alternatives as $key => $value) {
			$alternatives_query .= "INSERT INTO singlechoice_alternatives VALUES (UNHEX('$id'), '" . $value->alternative . "', $key);";
			$this->alternatives[] = array("alternative" => $value->alternative, "position" => $key);
		}
		if (parent::$db->multi_query("INSERT INTO posts VALUES (UNHEX('$id'), 'answer', UTC_TIMESTAMP()); INSERT INTO answer_posts VALUES (UNHEX('$id'), UNHEX('$organization_id'), 'multichoice', '$question');" . $alternatives_query)) {
			while (parent::$db->more_results()) {parent::$db->next_result();} // Flush the queries

			// Get the correct UTC_TIMESTAMP() value which was generated in the previous query
			if ($result = parent::$db->query("SELECT created FROM posts WHERE post_id = UNHEX('$id');")) {
				$row = $result->fetch_assoc();
				$this->created = $row['created'];
			}

			$this->post_id = $this->hex2base64url($id);
			$this->question = $question;
			$this->organization_id = $this->hex2base64url($organization_id);
		} else {
			// TODO: Database error
		}
	}

	public function jsonSerialize()
	{
		return array(
			"post_id" => $this->post_id,
			"post_type" => $this->post_type,
			"answer_type" => $this->answer_type,
			"created" => $this->created,
			"question" => $this->question,
			"organization_id" => $this->organization_id,
			"alternatives" => $this->alternatives
		);
	}
}
?>
