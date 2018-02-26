<?php
namespace Model;

class Post extends Model
{
	protected $post_id;
	protected $post_type;
	protected $created;

	public function getId()
	{
		return $this->post_id;
	}
}
?>
