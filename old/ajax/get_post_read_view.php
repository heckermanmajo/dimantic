<?php

use cls\data\post\Post;

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

$post_id = $_GET["id"] ?? throw new Exception("No post id given.");

$post = Post::get_one(
  App::get_connection(),
  "SELECT * FROM `Post` WHERE `id` = ?;",
  [$post_id]
);

if($post == null){
  echo "Post not found.";
  exit();
}

$post->echo_read_all_display_card();