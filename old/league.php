<?php

use cls\data\post\Post;

include $_SERVER['DOCUMENT_ROOT'] . "/cls/App.php";

if (!App::somebody_is_logged_in()) {
  header("Location: /index.php");
  exit();
}

App::head_html();

$id = $_GET["id"] ?? exit("no id given");

$league = \cls\data\league\AttentionLeague::get_by_id(
  App::get_connection(),
  $id
);

?>
  <header class="w3-margin w3-padding">
    <a class='button' href='/index.php'> Zurück </a> <br>
    <a class="button" href="/league.php?id=<?= $_GET["id"] ?>&tab=current_season"> Current Season (matches or Qualifying) </a>&nbsp;
    <a class="button" href="/league.php?id=<?= $_GET["id"] ?>&tab=old_seasons"> Old Seasons </a>
  </header>
<hr>
<div class="w3-card-4 w3-margin w3-padding">
  <p><?=$league->league_description?></p>
  <pre><?= json_encode($league, JSON_PRETTY_PRINT) ?></pre>
</div>

<?php


$current_season = $league->get_latest_season();

switch ($_GET["tab"] ?? "") {

  case "old_seasons":
    $seasons = \cls\data\league\AttentionLeagueSeason::get_array(
      App::get_connection(),
      "SELECT * FROM AttentionLeagueSeason WHERE attention_league_id = ?;",
      [$id]
    );
    foreach ($seasons as $season) {
      if($season->id != $current_season->id){
        $season->echo_simple_card();
      }
    }
    break;

  case "one_season":
    ?>
    <h3>One Season</h3>
    <?php
    break;

  default:
    $current_season->echo_simple_card();
    $all_qualified_posts = $current_season->get_qualified_posts();
    foreach ($all_qualified_posts as $post) {
      $post->echo_overview_display_card();
    }
    break;
}
App::put_logs();