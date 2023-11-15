<?php

use cls\data\tree\Tree;

include $_SERVER['DOCUMENT_ROOT'] . '/cls/App.php';


App::head_html();
if (!App::somebody_is_logged_in()) {
  header('Location: /index.php');
  exit();
}

$id = $_GET["id"] ?? 0;

# if 0 is given as id, or no id is given, then we are creating a new tree
if ($id == 0) {
  $result = Tree::create_tree_request();
  if ($result instanceof \cls\RequestError) {
    echo $result->dev_message;
    exit(); # todo: make better
  }
  else {
    $tree = $result;
    header('Location: /tree.php?id=' . $tree->id);
    exit();
  }
}


$_SESSION["uri_history"][] = "/tree.php?id=" . $_GET["id"];


$tree = \cls\data\tree\Tree::get_by_id(App::get_connection(), $id);

$possible_root_node = \cls\data\tree\Node::get_one(
  App::get_connection(),
  "SELECT * FROM `Node` WHERE `owner_tree_id` = ? AND `parent_node_id` = 0;",
  [$tree->id]
);

if ($possible_root_node == null) {

  $post = new \cls\data\post\Post();
  $post->content = "Root post for Tree";
  $post->author_id = App::get_current_account()->id;
  $post->created_at = date("Y-m-d H:i:s");
  $post->save(App::get_connection());

  $root_node = new \cls\data\tree\Node();
  $root_node->owner_tree_id = $tree->id;
  $root_node->parent_node_id = 0;
  $root_node->ref_post_id = $post->id;
  $root_node->save(App::get_connection());

}
else {
  $root_node = $possible_root_node;
}


$root_node->get_child_nodes();

?>
  <header>
    <br>
    <?php
    echo " <a class='button w3-margin' href='/index.php'> < Zurück </a> ";

    ?>
    <h3>One Tree</h3>
  </header>

  <div id="select_posts_for_new_node">
    <div id="select_posts_for_new_node_content">

    </div>
  </div>
  <!--
<pre><?= json_encode($tree, JSON_PRETTY_PRINT) ?></pre>
<hr>
<pre><?= json_encode($root_node, JSON_PRETTY_PRINT) ?></pre>
-->
  <div class="w3-row" id="tree_view_content">
    <div class="w3-half">

      <script>
        $(document).ready(function () {
          tree(
            '<?=$tree->get_css_id_for_expanded_tree()?>',
            '<?=$tree->type?>',
            <?=$tree->author_id === App::get_current_account()->id ? "true" : "false"?>,
            false, // todo. create function for this as tree method
            <?=json_encode($root_node->get_me_as_json_for_js_tree(), JSON_PRETTY_PRINT)?>
          );
        })
      </script>
      <div id="<?= $tree->get_css_id_for_expanded_tree() ?>"></div>

    </div>
    <div class="w3-half" id="right_tree_details">

    </div>
  </div>

  <br><br><br>

<?php
App::page_cleanup();
App::put_logs();
