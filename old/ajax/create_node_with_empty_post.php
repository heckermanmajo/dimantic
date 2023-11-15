<?php

include $_SERVER['DOCUMENT_ROOT'] . '/cls/App.php';

$parent_node_id = $_POST["parent_node_id"] ?? throw new Exception("No parent node given.");


$possible_parent_node = \cls\data\tree\Node::get_by_id(App::get_connection(), $parent_node_id);

if ($possible_parent_node == null) {
  throw new Exception("No parent node found.");
}

$possible_tree_node = \cls\data\tree\Tree::get_by_id(App::get_connection(), $possible_parent_node->owner_tree_id);

if ($possible_tree_node == null) {
  throw new Exception("No tree found for parent node.");
}

# todo: check access rights ...

$post = new \cls\data\post\Post();
$post->content = "[empty]";
$post->author_id = App::get_current_account()->id;
$post->created_at = date("Y-m-d H:i:s");
$post->save(App::get_connection());

$child = new \cls\data\tree\Node();
$child->owner_tree_id = $possible_tree_node->id;
$child->parent_node_id = $possible_parent_node->id;
$child->ref_post_id = $post->id;
$child->position = 0;
$child->save(App::get_connection());