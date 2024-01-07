<?php
declare(strict_types=1);

use cls\App;
use cls\data\dialoge\Dialogue;
use cls\data\space\Space;
use cls\HtmlUtils;

require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

try {

  App::init_context(basename_file: basename(path:__FILE__));
  $app = App::get();
  [$log, $warn, $err, $todo]
    = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  if (!$app->somebody_logged_in()) {
    header("Location: /index.php");
  }

  $app->handle_action_requests();

  HtmlUtils::head();
  HtmlUtils::main_header();

  ?>
  <div>

      <?php
      $all_my_dialogues = Dialogue::get_my_dialoges(
        0, 20, $app
      );
      $counter = 0;
      $mappedOn3 = [];
      foreach ($all_my_dialogues as $dialoge) {
        if(!isset($mappedOn3[(int)($counter / 3) ])){
          $mappedOn3[(int)($counter / 3) ] = [];
        }
        $mappedOn3[(int)($counter / 3) ][] = $dialoge->get_overview_card(
          $app
        );
        $counter++;
        if ($counter >= 9) {
          break;
        }
      }

      foreach ($mappedOn3 as $row) {
        ?>
        <div class="w3-row">
          <?php
          foreach ($row as $card) {
            ?>
            <div class="w3-third">
              <?= $card ?>
            </div>
            <?php
          }
          ?>
        </div>
        <?php

      }

      ?>
      <a href="/search.php?myConversations" style="font-size: 80%"> All my conversations</a>


    <?php
    $all_spaces = Space::getAllSpaces($app);
    $all_spaces = array_reverse($all_spaces);
    foreach ($all_spaces as $space) {
      echo $space->getDisplayCard($app);
    }
    ?>


    <div onclick="FN_TOGGLE('home_info')" class="info-card" id="home_info" style="display:none">
      <p>Home info .... </p>
    </div>

  </div>
  <?php


  HtmlUtils::footer($app);
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}