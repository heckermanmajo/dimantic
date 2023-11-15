<?php

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";


$new_parent_id = $_POST["parent_node_id"] ?? throw new Exception("No new parent id given.");
$post_id = $_POST["post_id"] ?? throw new Exception("No post id given.");

# todo: check all stuff for security and errors

$possible_new_parent = \cls\data\tree\Node::get_one(
  App::get_connection(),
  "SELECT * FROM `Node` WHERE `id` = ?;",
  [$new_parent_id]
);

if ($possible_new_parent == null) {
  echo "New parent not found.";
  exit();
}

$node = new \cls\data\tree\Node();
$node->owner_tree_id = $possible_new_parent->owner_tree_id;
$node->parent_node_id = $new_parent_id;
$node->ref_post_id = $post_id;
$node->position = 0;
$node->save(App::get_connection());

echo json_encode($node->get_me_as_json_for_js_tree(), JSON_PRETTY_PRINT);

