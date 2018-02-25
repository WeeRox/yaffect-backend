<?php
namespace Model;

use JsonSerializable;

class Post extends Model implements JsonSerializable
{
	private $post_id;
	private $organization_id;
	private $question;
}
?>
