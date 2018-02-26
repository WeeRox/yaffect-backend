<?php
namespace Model;

use JsonSerializable;

class QuestionPost extends Post implements JsonSerializable
{
	private $user_id;

	public function create($question, $user_id, $organization_id)
	{
		$id = $this->uuid2hex($this->generateUUIDv4());
		$user_id = $this->base64url2hex($user_id);
		$organization_id = $this->base64url2hex($organization_id);
		if (parent::$db->multi_query("INSERT INTO posts VALUES (UNHEX('$id'), 'question', UTC_TIMESTAMP()); INSERT INTO question_posts (post_id, user_id, organization_id, question) VALUES (UNHEX('$id'), UNHEX('$user_id'), UNHEX('$organization_id'), '$question');")) {
			$this->post_id = $this->hex2base64url($id);
			$this->question = $question;
			$this->organization_id = $this->hex2base64url($organization_id);
			$this->user_id = $this->hex2base64url($user_id);
		} else {
			// TODO: Database error
			echo parent::$db->error;
		}
	}

	public function jsonSerialize()
	{
		return array(
			"post_id" => $this->post_id,
			"post_type" => "question",
			"question" => $this->question,
			"organization_id" => $this->organization_id,
			"user_id" => $this->user_id
		);
	}
}
?>
