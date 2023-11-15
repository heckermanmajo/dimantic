<?php

namespace cls\data\tree;

use App;
use cls\data\post\Post;
use cls\DataClass;

class Node extends DataClass {
  var int $owner_tree_id = 0;
  var int $parent_node_id = 0;
  var int $ref_post_id = 0;
  /** @deprecated */
  var int $order = 0;
  var int $position = 0;

  var array $__child_nodes = [];
  var ?Post $__post = null;


  function _get_child_nodes(int $max_depth = 10) {
    if ($max_depth <= 0) {
      return;
    }
    $this->__child_nodes = Node::get_array(
      App::get_connection(),
      "SELECT * FROM `Node` WHERE `parent_node_id` = ? ORDER BY `position` ASC;",
      [$this->id]
    );

    // get all posts
    $posts = Post::get_array(
      App::get_connection(),
      "SELECT * FROM `Post` WHERE `id` IN (
            SELECT ref_post_id FROM `Node` WHERE `parent_node_id` = ?);",
      [$this->id]
    );

    $posts_on_id = [];
    foreach ($posts as $post) {
      $posts_on_id[$post->id] = $post;
    }

    foreach ($this->__child_nodes as $child_node) {
      $child_node->__post = $posts_on_id[$child_node->ref_post_id] ?? null;
    }

    foreach ($this->__child_nodes as $child) {
      $child->_get_child_nodes($max_depth - 1);
    }
  }

  function get_child_nodes(): void {
    $post = Post::get_one(
      App::get_connection(),
      "SELECT * FROM `Post` WHERE `id` =?;",
      [$this->ref_post_id]
    );
    $this->__post = $post;
    $this->_get_child_nodes();
  }

  function get_me_as_json_for_js_tree(int $depth = 10): array {
    $json = [
      "id" => $this->id,
      "ref" => $this->ref_post_id,
      "text" => ($this->__post == null) ? "[no title]" : $this->__post->get_title() . " ($this->ref_post_id) ",
      "children" => [],
      "state" => [
        "opened" => true,
      ]
    ];

    if ($depth <= 0) {
      return $json;
    }

    foreach ($this->__child_nodes as $child) {
      $json["children"][] = $child->get_me_as_json_for_js_tree($depth - 1);
    }

    return $json;
  }

  function get_last_child(): ?Node {
    $last_child = Node::get_one(
      App::get_connection(),
      "SELECT * FROM `Node` WHERE `parent_node_id` = ? ORDER BY `order` DESC LIMIT 1;",
      [$this->id]
    );
    return $last_child;
  }
}