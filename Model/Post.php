<?php
namespace Model;

class Post extends Model
{
	protected $post_id;
	protected $organization_id;
	protected $question;

	public function getId()
	{
		return $this->post_id;
	}
}
?>
