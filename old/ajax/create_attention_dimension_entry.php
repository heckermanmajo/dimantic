<?php
include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

$post_id = (int) $_POST["post_id"] ?? throw new Exception("No post_id given.");

$attention_dimension_id = (int) $_POST["attention_dimension_id"] ?? throw new Exception("No attention_dimension_id given.");

$post = \cls\data\post\Post::get_one(
  App::get_connection(),
  "SELECT * FROM `Post` WHERE `id` = ?;",
  [$post_id]
);

if(!$post){
  throw new Exception("Post not found.");
}

if($post->author_id != App::get_current_account()->id){
  throw new Exception("You are not the author of this post.");
}

$possible_entry = \cls\data\AttentionDimensionEntry::get_one(
  App::get_connection(),
  "SELECT * FROM `AttentionDimensionEntry` WHERE `post_id` = ? and attention_dimension_id = ?;",
  [$post_id,$attention_dimension_id]
);

if($possible_entry){
  throw new Exception("This post is already an entry in this attention dimension.");
}

$entry = new \cls\data\AttentionDimensionEntry();
$entry->post_id = $post_id;
$entry->author_id = App::get_current_account()->id;
$entry->attention_dimension_id = $attention_dimension_id;
$entry->created_at = date("Y-m-d H:i:s");
$entry->save(App::get_connection());

return json_encode(
  $entry
);