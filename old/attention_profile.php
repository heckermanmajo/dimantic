<?php

use cls\controller\request\attention_history\DeletePostEntryFromHistory;use cls\data\attention_profile\AttentionHistoryEntry;use cls\data\post\Post;use cls\RequestError;

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

if (!App::somebody_is_logged_in()) {
  header('Location: /index.php');
  exit();
}

if (isset($_POST["action"])) {
  if ($_POST["action"] == "delete_post_id_from_history") {
    $result = DeletePostEntryFromHistory::execute();
    if ($result instanceof RequestError) {
      echo $result->user_message;
      # todo: handle error message
    }
    else {
      header("Location: /index.php?tab=history");
      exit();
    }
  }
}

$attention_profile = App::$attention_profile;

App::head_html();

?>
  <header class="w3-margin">
    <a class='button' href='/index.php'> Zurück </a>
    &nbsp;
    <b><?=App::$attention_profile->title?></b>
    <!--<pre>
      <?= json_encode($attention_profile, JSON_PRETTY_PRINT) ?>
</pre>-->
    <hr>
    <a class="button" href="/attention_profile.php"> Overview </a> &nbsp;
    <a class="button" href="/attention_profile.php?tab=history"> History </a>&nbsp;
    <a class="button" href="/attention_profile.php?tab=edit"> Edit </a>&nbsp;
    <a class="button" href="/attention_profile.php?tab=attention_dimensions"> Attention Dimensions of interest </a>&nbsp;
  </header>


<?php
switch ($_GET["tab"] ?? "") {
  case "edit":
    echo App::$attention_profile->get_manage_card(with_select_form: false);
    break;
  case "attention_dimensions":
    ?>
  <div id="overlay_placeholder_select_dimensions_for_interest" style="display: none" class="w3-margin">
    <button
      class="delete-button"
      onclick="window.location.reload();">Close</button>
    <div id="overlay_placeholder_select_dimensions_for_interest_content">
    </div>
    <button
      class="delete-button"
      onclick="window.location.reload();">Close</button>
  </div>
  <div id="list_of_selected_attention_dimensions"  class="w3-margin">
  <button
    class="button"
    onclick="
    $.post(
      '/ajax/select_attention_dimensions_for_interest.php',
    ).done(function (data) {
      $('#overlay_placeholder_select_dimensions_for_interest_content').html(data);
      $('#overlay_placeholder_select_dimensions_for_interest').show();
      $('#list_of_selected_attention_dimensions').hide();
    });"
    >Add Attention Dimension</button>
  <?php
    $all_attention_dimensions_of_this_profile = \cls\data\league\AttentionDimension::get_array(
      App::get_connection(),
      "SELECT * FROM AttentionDimension WHERE AttentionDimension.id IN (
            SELECT AttentionDimensionInterestEntry.attention_dimension_id 
            FROM AttentionDimensionInterestEntry WHERE AttentionDimensionInterestEntry.attention_profile_id = ?);",
      [
        App::$attention_profile->id
      ]
    );

    foreach ($all_attention_dimensions_of_this_profile as $dimension) {
      ?>
      <div class="w3-card-4 w3-margin w3-padding" id="attention_dimension_interest_<?=$dimension->id?>">
        <h3><?= $dimension->title ?></h3>
        <p><?= $dimension->description ?></p>
        <button class="delete-button"
          onclick="
            $.post(
              '/ajax/delete_attention_dimension_interest_entry.php',
              {
                attention_dimension_id: <?= $dimension->id ?>
              }
            ).done(function (data) {
              $('#attention_dimension_interest_<?=$dimension->id?>').html(data);
            });
          "
        > Remove from this Attention Profile </button>
      </div>
      <?php
    }

    echo "</div>"; # end of list_of_selected_attention_dimensions

    break;
  case "history":
    ?>
    <div class="w3-row w3-margin">
      <div class="w3-half">
        <?php
        $all_attention_nodes = AttentionHistoryEntry::get_array(
          App::get_connection(),
          "SELECT * FROM `AttentionHistoryEntry` WHERE `attention_path_id` = ? ORDER BY `counter` DESC;",
          [App::$attention_profile->id]
        );
        # get_posts_by_attention_profile
        $posts_of_attention_profile = Post::get_posts_by_attention_profile(App::$attention_profile->id);
        $posts_on_id = [];
        foreach ($posts_of_attention_profile as $post) $posts_on_id[$post->id] = $post;

        foreach ($all_attention_nodes as $node) {
          ?>
          <a

            style="text-decoration: none
            <?php if (isset($_GET["id"])) {
              if ($_GET["id"] == $node->post_id) {
                echo ";color: #00bcd4;";
              }
            } ?>
              "

            href="attention_profile.php?tab=history&id=<?= $node->post_id ?>">
            <b>(<?= $node->counter ?>)</b> - -
            <?= $node->post_id ?>
            <i><?= str_replace("<br>", "",
                $posts_on_id[$node->post_id]->get_title()) ?></i>
          </a>
          <br>
          <?php
        }
        ?>
      </div>
      <div class="w3-half">

        <?php
        if (isset($_GET["id"])) {
          ?>
          <form method="post" style="display: inline">
            <input type="hidden" name="action" value="delete_post_id_from_history">
            <input type="hidden" name="post_id" value="<?= $_GET["id"] ?>">
            <button class="delete-button"> Remove from attention history</button>
          </form>
          <?php
          $post = Post::get_one(
            App::get_connection(),
            "SELECT * FROM `Post` WHERE `id` = ?;",
            [$_GET["id"]]
          );
          if ($post !== null) {
            $post->echo_overview_display_card();
          }
          else {
            echo "Post not found.";
          }
        }
        ?>
        <!--<h3>One post- with details</h3>--->
      </div>
    </div>
    <?php break; ?>
  <?php
  default:
    echo "Attention Overview";
    break;
}