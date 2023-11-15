<?php

namespace cls\data\tree;

use App;
use cls\data\post\Post;
use cls\DataClass;

class Tree extends DataClass {

  use CreateTreeRequest;

  var int $author_id = 0;
  var int $root_node_id = 0;
  var string $type = ""; // content, thread

  /** @deprecated  */
  var string $name = "";

  function get_css_id_for_expanded_tree(): string {
    return "expanded_tree_" . $this->id;
  }

  /**
   * @return Node[]
   */
  function get_root_node(int $level = 10): Node {

    // get all nodes
    $nodes = Node::get_array(
      App::get_connection(),
      "SELECT * FROM `Node` WHERE `owner_tree_id` = ?;",
      [$this->id]
    );

    // get all posts
    $posts = Post::get_array(
      App::get_connection(),
      "SELECT * FROM `Post` WHERE `id` IN (
            SELECT ref_post_id FROM `Node` WHERE `owner_tree_id` = ?);",
      [$this->id]
    );

    // map all posts on ids
    $posts_by_id = [];
    foreach ($posts as $post) {
      $posts_by_id[$post->id] = $post;
    }

    $root_node = null;

    foreach ($nodes as $node) {
      $node->__post = $posts_by_id[$node->ref_post_id];
      if ($node->parent_node_id !== 0) {
        $nodes[$node->parent_node_id]->__child_nodes[] = $node;
      }
      else {
        $root_node = $node;
      }
    }

    # todo: repair the tree ...
    if ($root_node == null) {
      throw new \Exception("Root node not found");
    }

    return $root_node;

  }

  function echo_short_display_view(): void {
    $root_node = Node::get_one(
      App::get_connection(),
      "SELECT * FROM `Node` WHERE `owner_tree_id` = ? AND `parent_node_id` = 0;",
      [$this->id]
    );
    $root_post_id = $root_node->ref_post_id;
    $root_post = Post::get_by_id(App::get_connection(), $root_post_id);
    $number_of_nodes = Node::get_count(
      App::get_connection(),
      "SELECT COUNT(*) FROM `Node` WHERE `owner_tree_id` = ?;",
      [$this->id]
    );
    ?>
    <div class="w3-card-4 w3-padding w3-margin">
      <a href="/tree.php?id=<?= $this->id ?>" style="text-decoration: none">
        <h3><?= ($root_post == null) ? "[No Title]" : $root_post->get_title() ?> [<?=$number_of_nodes?>]</h3>
      </a>
      <pre><?= ($root_post == null) ? "[No Content]" : $root_post->get_short_desc()?></pre>
      <pre><?=json_encode($this, JSON_PRETTY_PRINT)?></pre>
    </div>
    <?php
  }

  function echo_expanded_tree_view(Node $root_node, int $level = 10): void {
    $root_node->get_child_nodes($level);
    ?>
    <script>
      $(document).ready(function () {
        tree(
          '<?=$this->get_css_id_for_expanded_tree()?>',
          '<?=$this->type?>',
          <?=$this->author_id === App::get_current_account()->id ? "true" : "false"?>,
          false, // todo. create function for this as tree method
          <?=json_encode($root_node->get_me_as_json_for_js_tree($level), JSON_PRETTY_PRINT)?>
        );
      })
    </script>
    <div id="<?= $this->get_css_id_for_expanded_tree() ?>"></div>
    <?php
  }
}