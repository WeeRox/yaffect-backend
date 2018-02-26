<?php
namespace Model;

use JsonSerializable;

class MultichoiceAnswerPost extends AnswerPost implements JsonSerializable
{
	public function __construct()
	{
		$this->answer_type = "multichoice";
	}

	public function create($question, $alternatives)
	{

	}
}
?>
