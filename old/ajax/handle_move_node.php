<?php

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

$node_id = $_POST["id"] ?? throw new Exception("No node id given.");
$new_parent_id = $_POST["new_parent_id"] ?? throw new Exception("No new parent id given.");
$new_position = $_POST["new_position_id"] ?? throw new Exception("No new position given.");

$node = \cls\data\tree\Node::get_one(
  App::get_connection(),
  "SELECT * FROM `Node` WHERE `id` = ?;",
  [$node_id]
);

if($node == null){
  echo "Node not found.";
  exit();
}

$node->parent_node_id = $new_parent_id;
$node->position = $new_position;
$node->save(App::get_connection());


