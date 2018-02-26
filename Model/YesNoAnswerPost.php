<?php
namespace Model;

use JsonSerializable;

class YesNoAnswerPost extends AnswerPost implements JsonSerializable
{

	public function __construct()
	{
		$this->answer_type = "yes_no";
	}

	public function create($question, $organization_id)
	{
		$id = $this->uuid2hex($this->generateUUIDv4());
		$organization_id = $this->base64url2hex($organization_id);
		if (parent::$db->query("INSERT INTO posts VALUES (UNHEX('$id'), 'answer', UTC_TIMESTAMP()); INSERT INTO answer_posts VALUES (UNHEX('$id'), UNHEX('$organization_id'), 'yes_no', '$question');")) {
			$this->post_id = $this->hex2base64url($id);
			$this->question = $question;
			$this->organization_id = $this->hex2base64url($organization_id);
		} else {
			//TODO: Database error
		}
	}

	public function jsonSerialize()
	{
		return array(
			"post_id" => $this->post_id,
			"post_type" => $this->post_type,
			"answer_type" => $this->answer_type,
			"question" => $this->question,
			"organization_id" => $this->organization_id
		);
	}
}
?>
