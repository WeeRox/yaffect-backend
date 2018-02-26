<?php
namespace Model;

class AnswerPost extends Post
{
	protected $organization_id;
	protected $answer_type;
	protected $question;

	public function __construct()
	{
		$this->post_type = "answer";
	}
}
?>
