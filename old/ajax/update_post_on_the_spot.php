<?php

include $_SERVER['DOCUMENT_ROOT'] . '/cls/App.php';


$content = $_POST["content"] ?? throw new Exception("No content given.");
$post_id = $_POST["id"] ?? throw new Exception("No post id given.");

$possible_post = \cls\data\post\Post::get_by_id(App::get_connection(), $post_id);

if ($possible_post == null) {
  throw new Exception("No post found.");
}

$possible_post->content = $content;
\cls\Interpreter::execute_once_commands($possible_post);

echo $possible_post->content;