<?php
include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

use cls\controller\request\post\PublishPost;
use cls\data\post\Post;
use cls\RequestError;

if (!App::somebody_is_logged_in()) {
  header("Location: /index.php");
  exit();
}

$id = $_GET["id"] ?? 0;

// if this page is opened without id, then create a new post
if ($id == 0) {
  // create new post as answer to some post
  if (isset($_GET["answer_id"])) {
    $result = \cls\controller\request\post\CreateAnswerPost::execute();
    if ($result instanceof RequestError) {
      echo $result->dev_message;
      # todo: handle better ....
      exit();
    }
    else {
      $new_post = $result;
      header("Location: /post.php?id=$new_post->id");
      exit();
    }

  }
  // create new post without parent
  else {
    $result = \cls\controller\request\post\CreatePost::execute(
      idea_space_id: $_GET["idea_space"] ?? 0,
    );
    if ($result instanceof RequestError) {
      echo $result->dev_message;
      # todo: handle better ....
      exit();
    }
    else {
      $new_post = $result;
      header("Location: /post.php?id=$new_post->id");
      exit();
    }
  }
}

if (isset($_POST["action"])) {

  if ($_POST["action"] == "publish_post") {
    $result = PublishPost::execute();
    if ($result instanceof RequestError) {
      echo $result->dev_message;
    }
    else {
      // ...
    }
  }

  if ($_POST["action"] == "select_league_to_compete") {
    $result = \cls\controller\request\attention_league\ApplyWithPostForSeason::execute();
    if ($result instanceof RequestError) {
      echo $result->dev_message;
    }
    else {
      // ...
    }
  }

}

App::head_html(
/** @lang CSS */ "
#overlay {
  position: fixed; /* Sit on top of the page content */
  display: none; /* Hidden by default */
  width: 100%; /* Full width (cover the whole page) */
  height: 100%; /* Full height (cover the whole page) */
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0,0,0,0.5); /* Black background with opacity */
  z-index: 2; /* Specify a stack order in case you're using a different order for other elements */
  cursor: pointer; /* Add a pointer on hover */
}

#overlay-text{
  position: absolute;
  top: 50%;
  left: 50%;
  font-size: 50px;
  color: white;
  transform: translate(-50%,-50%);
  -ms-transform: translate(-50%,-50%);
}

");


$post = Post::get_one_by_id($id);

// add attention node
$attention_path = App::$attention_profile;
$possible_node = \cls\data\attention_profile\AttentionHistoryEntry::get_one(
  App::get_connection(),
  "SELECT * FROM `AttentionHistoryEntry` WHERE `attention_path_id` = :attention_path_id AND `post_id` = :post;",
  [
    "attention_path_id" => $attention_path->id,
    "post" => $post->id
  ]
);

if ($possible_node !== null) {
  $possible_node->counter++;
  $possible_node->save(App::get_connection());
}
else {
  $attention_node = new \cls\data\attention_profile\AttentionHistoryEntry();
  $attention_node->attention_path_id = $attention_path->id;
  $attention_node->post_id = $post->id;
  $attention_node->create_date = date("Y-m-d H:i:s");
  $attention_node->counter = 1;
  $attention_node->save(App::get_connection());
}

$posts = $post->get_post_parent_history();
$posts = array_reverse($posts);
echo "<div class='w3-padding'>";

if (isset($_SESSION["uri_history"]) && count($_SESSION["uri_history"]) > 0) {
  echo "<a class='button' href='" . array_pop($_SESSION["uri_history"]) . "'> Zurück </a> ";
}
else {
  echo "<a class='button' href='/index.php'> Zurück </a> ";
}

foreach ($posts as $parent_post) {
  ?>
  <a href="/post.php?id=<?= $parent_post->id ?>"><?= $parent_post->get_title() ?></a> >
  <?php
}
echo "</div>";
?>
<hr>
<div class="w3-row">
  <div class="w3-half">
    <div class="w3-padding">
      <a class="button" href="/post.php?id=<?= $id ?>">Readview</a>
      <a class="button" href="/post.php?id=<?= $id ?>&tab=answer">Answer</a>
      <?php if ($post->author_id == App::get_current_account()->id) { ?>
        <a class="button" href="/post.php?id=<?= $id ?>&tab=edit">Edit</a>
      <?php } ?>
      <a href="/post.php?id=<?= $id ?>&tab=select_attention_league" class="button">Select Attention League</a>
      <!--<a class="button" href="/post.php?id=<?= $id ?>&tab=attention_dimension">Attention Dimension</a>-->
      <hr>
    </div>


    <?php
    switch ($_GET["tab"] ?? "") {
    case "edit":
      ?>
      <div class="w3-card w3-margin w3-padding">
        <?php if ($post->published == 0): ?>
          <form method="post" style="display: inline" class="w3-margin">
            <input type="hidden" name="post_id" value="<?= $post->id ?>">
            <input type="hidden" name="action" value="publish_post">
            <button class="button">Publish</button>
          </form>
        <?php else: ?>
          <p class="w3-margin"><b><i>Published</b></i></p>
        <?php endif; ?>
        <?php
        if ($post->liga_season_id > 0) {
          $attention_league_of_this_post = $post->get_league();
          $season = $post->get_season();
          ?>
          <div>
            <h4>Liga, season</h4>
            <pre>
              <?= $attention_league_of_this_post->league_description ?>
            </pre>
            <pre>
              <?= $season->season_description ?>
            </pre>
          </div>
          <?php
        }
        else {
          ?>
          <button> Apply for a League with this post</button>
          <?php
        }
        ?>

        <p></p>

      </div>

      <div class="w3-card-4 w3-margin" id="edit_form">
        <div>
          <script>
            $(document).ready(function () {
              let textarea = document.getElementById('post_edit_textarea');
              textarea.style.height = textarea.scrollHeight + 'px';
            });
          </script>
          <textarea
            id="post_edit_textarea"
            oninput="this.style.height = 0; this.style.height = this.scrollHeight + 'px';"
            style="width: 100%" name="content"><?= $post->content ?></textarea>
          <button
            onclick="
              let textarea = document.getElementById('post_edit_textarea');
              $.post(
              '/ajax/update_post_on_the_spot.php',
              {
              id: <?= $post->id ?>,
              content: document.getElementById('post_edit_textarea').value
              },
              ).done(function (data) {
              textarea.value = data;
              }).fail(function (data) {
              console.log('error');
              });"
            class="button"
            style="width: 100%"
          >
            Save
          </button>
        </div>
        <pre><?= json_encode($post, JSON_PRETTY_PRINT) ?></pre>
      </div>
    <?php
    break;

    #########################################################################
    case "answer":
    ?>
      <a class="button w3-margin" href="/post.php?answer_id=<?= $post->id ?>">Answer</a>
    <?php

    $direct_children = Post::get_direct_children_of_post($post->id);
    foreach ($direct_children as $child) {
      $child->echo_overview_display_card();
    }
    break;


    case "select_attention_league":

    if ($post->liga_season_id != 0){
    // select the league and display it

    $league = $post->get_league();
    ?>
      <div class="infobox w3-margin w3-padding">
        <p> This post is competing in league <b><?= $league->get_title() ?></b></p>
        <p> The following dimension your content need to master:</p>
      </div>
      <?php
      $dimensions = $league->get_rating_dimensions();
      foreach ($dimensions as $dimension){
        $dimension->echo_edit_card();
      }

    }
    else {

      $leagues = \cls\data\league\AttentionLeague::get_array(
        App::get_connection(),
        "SELECT * FROM `AttentionLeague`;",
        []
      );

      ?>
      <div class="infobox w3-margin w3-padding">
        <h3> ℹ️ Select attention league to compete in </h3>
        <p>You can select a league for your post to compete in - choose wisely - you
          can only compete in one league.</p>
      </div>
      <?php
    foreach ($leagues as $league) {
      # todo: select currently open season, only
      #       if season is open display as selectable
      #       otherwise display grayed out entry
      #       later a possible alert when the next season in a
      #       league is open,

      $latest_season = $league->get_latest_season();
    if ($latest_season->state == "open"):
      ?>
      <div class="w3-card w3-margin w3-padding">
        <h4><?= $league->league_description ?></h4>
        <form method="post">
          <input type="hidden" name="action" value="select_league_to_compete">
          <input type="hidden" name="season_id" value="<?= $latest_season->id ?>">
          <input type="hidden" name="post_id" value="<?= $post->id ?>">
          <button class="button"> Compete in this League</button>
        </form>
      </div>
    <?php
    else:
      ?>
      <div class="w3-card w3-margin w3-padding" style="color: darkgray !important;">
        <h4><?= $league->league_description ?></h4>
        <p>Season is closed</p>
        <button>Alert on next open season - does not work yet</button>
      </div>
    <?php
    endif;
    ?>

    <?php
    }
    }
    break;


    #########################################################################
    /*case "attention_dimension":

    $attention_dimension_entries = AttentionDimensionEntry::get_array(
      App::get_connection(),
      "SELECT *,
          (SELECT title FROM AttentionDimension WHERE AttentionDimension.id = AttentionDimensionEntry.attention_dimension_id) AS _attention_dimension_title,
          (SELECT description FROM AttentionDimension WHERE AttentionDimension.id = AttentionDimensionEntry.attention_dimension_id) AS _attention_dimension_description
       FROM `AttentionDimensionEntry` WHERE `post_id` = ?;",
      [$post->id]
    );

    foreach ($attention_dimension_entries as $attention_dimension_entry) {
      $attention_dimension_entry->put_display_card();
    }

    if ($post->author_id == App::get_current_account()->id) {
    ?>
      <script>
        function create_attention_dimension_entry(attention_dimension_id, button) {
          $.post(
            "/ajax/create_attention_dimension_entry.php",
            {
              post_id: <?= $post->id ?>,
              attention_dimension_id: attention_dimension_id
            },
            function (data) {
              alert(data);
              button.parentNode.innerHTML = "Added to attention dimension.";
            }
          );
        }
      </script>
      <div style="display: none; overflow: scroll" id="overlay">
        <div id="overlay_text">
          <h3>Alle Attention dimensions ... </h3>
          <?php
          $attention_dimensions = \cls\data\league\AttentionDimension::get_array(
            App::get_connection(),
            "SELECT *, 
            (SELECT COUNT(*) FROM AttentionDimensionEntry WHERE AttentionDimensionEntry.attention_dimension_id = AttentionDimension.id ) FROM `AttentionDimension`;",
            []
          );
          foreach ($attention_dimensions as $attention_dimension) {
            # dont show already added attention dimensions
            foreach ($attention_dimension_entries as $attention_dimension_entry) {
              if ($attention_dimension_entry->attention_dimension_id == $attention_dimension->id) {
                continue 2;
              }
            }
            ?>
            <div class="w3-card w3-margin w3-padding" style="background-color: #1d1e20">
              <h6><?= $attention_dimension->title ?></h6>
              <pre><?= $attention_dimension->description ?></pre>
              <p> Number of posts in category: <?= $attention_dimension->_number_of_posts ?> </p>
              <button onclick="create_attention_dimension_entry(<?= $attention_dimension->id ?>, this)"
                      class="button"> Select this Dimension
              </button>
            </div>
            <?php
          }
          if (count($attention_dimensions) == 0) {
            echo "No attention dimensions found.";
          }
          ?>
        </div>
        <button class="delete-button" onclick="window.location = window.location"> Close</button>
      </div>
      <script>
        function open_attention_dimension_select_overview() {
          document.getElementById("overlay").style.display = "block";
        }
      </script>
      <button class="button" onclick="open_attention_dimension_select_overview(<?= $post->id ?>)"> Add Attention
        Dimension to Post
      </button>
    <?php
    }
    ?>

    <?php
    break;*/
    #########################################################################
    default:
    ?>
      <div class="w3-card-4 w3-padding w3-margin">
        <?php $post->echo_meta_info_top_bar() ?>
        <h2><?= $post->get_title(-1) ?></h2>
        <?= \cls\Interpreter::execute_always_commands($post) ?>
      </div>
    <?php
    }
    ?>


  </div>
  <div class="w3-half">
    <?php
    if (isset($_GET["tab"]) && $_GET["tab"] == "edit") {
      $parent_id = $post->parent_post_id;
      if ($parent_id > 0) {
        $parent_post = Post::get_by_id(App::get_connection(), $parent_id);
        $parent_post->echo_read_all_display_card();
      }
    }
    ?>
  </div>
</div>
