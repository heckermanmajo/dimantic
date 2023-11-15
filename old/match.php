<?php

use cls\data\league\PostMatch;

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

if (!App::somebody_is_logged_in()) {
  header("Location: /index.php");
  exit();
}

$id = $_GET["id"] ?? 0;

$match = PostMatch::get_one(
  App::get_connection(),
  "SELECT * FROM `Match` WHERE `id` = ?;",
  [$id]
);

$post_1 = $match->get_post_1();
$post_2 = $match->get_post_2();

?>

<header class="w3-margin w3-padding">

</header>

<div class="w3-row">
  <div class="w3-half">
    <?php $post_1->echo_read_all_display_card()?>
  </div>
  <div class="w3-half">
    <?php  $post_2->echo_read_all_display_card()?>
  </div>
</div>
