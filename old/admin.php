<?php

use cls\controller\request\attention_dimension\CreateAttentionDimensionRequest;
use cls\controller\request\attention_league\AddRatingDimensionToLeague;
use cls\controller\request\attention_league\CreateAttentionLeague;
use cls\controller\request\attention_league\RemoveRatingDimensionFromLeague;
use cls\controller\request\idea_space\CreateIdeaSpace;
use cls\data\league\AttentionDimension;
use cls\RequestError;

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

App::head_html();

if (!App::somebody_is_logged_in()) {
  header("Location: /index.php");
  exit();
}


if (isset($_POST["action"])) {
  if ($_POST["action"] == "create_attention_dimension") {
    $result = AttentionDimension::create_attention_dimension_request();
    if ($result instanceof RequestError) {
      echo $result->dev_message;
      # todo: handle better ....
      exit();
    }
    else {
      // pass ...
    }
  }

  if ($_POST["action"] == "create_idea_space") {
    $result = CreateIdeaSpace::execute();
    if ($result instanceof RequestError) {
      echo $result->dev_message;
      # todo: handle better ....
      exit();
    }
    else {
      // pass ...
    }
  }

  if ($_POST["action"] == "create_attention_league") {
    $result = CreateAttentionLeague::execute();
    if ($result instanceof RequestError) {
      echo $result->dev_message;
      # todo: handle better ....
      exit();
    }
    else {
      // pass ...
      header("Location: /admin.php?tab=leagues");
      exit();
    }
  }

  if ($_POST["action"] == "add_rating_dimension_to_league") {
    $result = AddRatingDimensionToLeague::execute();
    if ($result instanceof RequestError) {
      echo $result->dev_message;
      # todo: handle better ....
      exit();
    }
    else {
      header("Location: /admin.php?tab=leagues&id=" . $_POST["attention_league_id"]);
      exit();
    }
  }

  if ($_POST["action"] == "remove_rating_dimension_from_league") {
    $result = RemoveRatingDimensionFromLeague::execute();
    if ($result instanceof RequestError) {
      echo $result->dev_message;
      # todo: handle better ....
      exit();
    }
    else {
      header("Location: /admin.php?tab=leagues&id=" . $_POST["attention_league_id"]);
      exit();
    }
  }

  if($_POST["action"] == "start_fitting_seasons_for_ratings"){
    $all_leagues = \cls\data\league\AttentionLeague::get_array(
      App::get_connection(),
      "SELECT * FROM `AttentionLeague`;",
      []
    );
    foreach ($all_leagues as $league){
      $season = $league->get_latest_season();
      if($season->can_change_state_from_open_to_rating()){
        $league->start_league_rating_and_create_matches();
      }
    }
  }


}

?>

  <header class="w3-row">
    <div class="w3-half">
      <a class="w3-button" href="/admin.php"> Admin Task List </a>
      <a class="w3-button" href="/admin.php?tab=attention_dimensions"> Attention Dimensions </a>
      <a class="w3-button" href="/admin.php?tab=idea_spaces"> Idea-Spaces </a>
      <a class="w3-button" href="/admin.php?tab=leagues"> Leagues </a>
    </div>
    <div class="w3-half" style="text-align: right">
      <?php App::echo_nav_bar(); ?>
      <form method="post" style="display: inline-block" action="/api/logout.php">
        <input type="hidden" name="action" value="logout">
        <button class="w3-button w3-red"> Logout</button>
      </form>
    </div>
  </header>

<?php
switch ($_GET["tab"] ?? ""):
  #################################################################################
  case "attention_dimensions":
    ?>
    <div class="w3-card-4 w3-margin w3-padding">
      <form method="post">
        <input type="hidden" name="action" value="create_attention_dimension">
        <label>
          Name:<br>
          <input type="text" name="title">
        </label><br><br>
        <label>
          Description:<br>
          <textarea name="description"></textarea>
        </label><br><br>
        <button class="button"> Create</button>
      </form>
    </div>

    <?php
    $attention_dimensions = AttentionDimension::get_array(
      App::get_connection(),
      "SELECT *,
            (SELECT COUNT(*) FROM AttentionDimensionEntry WHERE AttentionDimensionEntry.attention_dimension_id = AttentionDimension.id ) FROM `AttentionDimension`;",
      []
    );
    foreach ($attention_dimensions as $attention_dimension) {
      $attention_dimension->put_edit_card();
    }
    ?>
    <?php break; ?>
  <?php

  #################################################################################
  case "idea_spaces":
    ?>
    <h2>Create Form Idea spaces</h2>
    <form method="post">
      <input type="hidden" name="action" value="create_idea_space">
      <textarea name="content"></textarea>
      <button class="button"> Create</button>
    </form>
    <?php

    $idea_spaces = \cls\data\idea_space\IdeaSpace::get_array(
      App::get_connection(),
      "SELECT * FROM `IdeaSpace`;",
      []
    );

    foreach ($idea_spaces as $league) {
      $league->put_display_card();
    }

    break; ?>
  <?php
  #################################################################################
  case "leagues":

    ?>
    <div class="w3-row">
      <div class="w3-half">
        <form method="post" class="w3-card w3-margin w3-padding">
          <input type="hidden" name="action" value="create_attention_league">
          <label>
            League description:<br>
            <textarea name="league_description"></textarea>
          </label><br><br>
          <label>
            Days per season:<br>
            <input type="number" name="days_per_season" value="14">
          </label><br><br>
          <label>
            Number of posts per season:<br>
            <input type="number" name="number_of_posts_per_season" value="10">
          </label><br><br>
          <button class="button"> Create</button>
        </form>
        <?php

        $all_leagues = \cls\data\league\AttentionLeague::get_array(
          App::get_connection(),
          "SELECT * FROM `AttentionLeague`;",
          []
        );

        foreach ($all_leagues as $league) {
          ?>
          <div class="w3-card-4 w3-margin w3-padding">
            <a href="/league.php?id=<?= $league->id ?>" style="text-decoration: none">
              <pre><?= json_encode($league, JSON_PRETTY_PRINT) ?></pre>
            </a>
          </div>
          <a href="/admin.php?tab=leagues&id=<?= $league->id ?>" class="button w3-margin">
            Edit rating dimensions of league
          </a>
          <?php
        }
        ?>
      </div>

      <div class="w3-half">
        <h3> details of one attention profile </h3>
        <?php
        if (isset($_GET["id"])) {
          $league = \cls\data\league\AttentionLeague::get_by_id(
            App::get_connection(),
            $_GET["id"]
          );
          ?>
          <div class="w3-card w3-margin w3-padding">
            <h4>Edit the league-form</h4>
            <form><textarea></textarea></form>
          </div>

          <p>List of rating dimensions, can be removed</p>

          <?php
          $added_dimensions = AttentionDimension::get_array(
            App::get_connection(),
            "SELECT * FROM `AttentionDimension` WHERE id IN 
            (SELECT attention_dimension_id FROM LeagueRatingDimension WHERE attention_league_id = ?);",
            [$league->id]
          );

          foreach ($added_dimensions as $dimension) {
            ?>
            <div class="w3-card w3-margin w3-padding">
              <h4><?= $dimension->title ?></h4>
              <p><?= $dimension->description ?></p>
              <form method="post">
                <input type="hidden" name="action" value="remove_rating_dimension_from_league">
                <input type="hidden" name="attention_league_id" value="<?= $league->id ?>">
                <input type="hidden" name="attention_dimension_id" value="<?= $dimension->id ?>">
                <button class="button"> Remove dimension from League</button>
              </form>
            </div>
            <?php
          }

          ?>

          <hr>
          <hr>

          <p>all non added rating dimensions, ready to be added</p>
          <?php
          $attention_dimensions = AttentionDimension::get_array(
            App::get_connection(),
            "SELECT * FROM `AttentionDimension` WHERE id NOT IN 
            (SELECT attention_dimension_id FROM LeagueRatingDimension WHERE attention_league_id = ?);",
            [$league->id]
          );

          foreach ($attention_dimensions as $attention_dimension) {
            ?>
            <div class="w3-card w3-margin w3-padding">
              <h4><?= $attention_dimension->title ?></h4>
              <p><?= $attention_dimension->description ?></p>
              <form method="post">
                <input type="hidden" name="action" value="add_rating_dimension_to_league">
                <input type="hidden" name="attention_league_id" value="<?= $league->id ?>">
                <input type="hidden" name="attention_dimension_id" value="<?= $attention_dimension->id ?>">
                <label>
                  Relevance multiplier:<br>
                  <input type="number" name="relevance_multiplier" value="1">
                </label><br><br>
                <button class="button"> Add dimension to League</button>
              </form>
            </div>
            <?php

          }

        }
        ?>
      </div>

    </div>
    <?php
    break;
  #################################################################################
  default:
    ?>
    <div class="w3-row">
      <div class="w3-half">
        <h3>Attention Profiles list </h3>
      </div>
      <div class="w3-half">
        <h3> details of one attention profile </h3>
      </div>
    </div>
  <?php

endswitch;
?>