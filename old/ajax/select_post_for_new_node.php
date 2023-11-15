<?php

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

$parent_node_id = $_POST["parent_node_id"] ?? throw new Exception("No parent node given.");

$possible_parent_node = \cls\data\tree\Node::get_by_id(App::get_connection(), $parent_node_id);

if ($possible_parent_node == null) {
  throw new Exception("No parent node found.");
}

$possible_tree_node = \cls\data\tree\Tree::get_by_id(App::get_connection(), $possible_parent_node->owner_tree_id);

if ($possible_tree_node == null) {
  throw new Exception("No tree found for parent node.");
}

# todo: check that user has access

$posts = \cls\data\post\Post::get_array(
  App::get_connection(),
  "SELECT * FROM `Post` WHERE `id` > 0 ORDER BY `id` DESC LIMIT 100;"
);

?>
<button class="delete-button" onclick="
  $('#tree_view_content').show();
                  $('#select_posts_for_new_node').hide();">Close
</button>
<br><br>
<div class="w3-row" style="overflow: scroll">

  <?php
  $count = 0;
  foreach ($posts

  as $post) {
  if ($count == 3){
  $count = 0;
  ?>
</div>
<div class="w3-row">
  <?php
  }
  ?>
  <div class="w3-third">
    <div class="w3-card" style="height: 30%">
      <div class="w3-container">
        <h3><?= $post->get_title(-1) ?></h3>
        <p><?= $post->get_short_desc() ?></p>
      </div>
      <script>
        function select_post<?=$post->id?>() {
          $.post(
            '/ajax/create_node.php',
            {
              post_id: <?= $post->id ?>,
              parent_node_id: <?= $possible_parent_node->id ?>
            },
          ).done(function (data) {
            window.location.reload();
          });
        }
      </script>
      <button
        onclick="select_post<?=$post->id?>()"
        class="w3-margin button">
        Create node from this post
      </button>
    </div>
  </div>
  <?php
  }
  ?>
</div>























