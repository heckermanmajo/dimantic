<?php
declare(strict_types=1);

use cls\App;
use cls\data\dialoge\Dialogue;
use cls\HtmlUtils;

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

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
    <div onclick="FN_TOGGLE('home_info')" class="info-card" id="home_info" style="display:none">
      <p>Home info .... </p>
    </div>
    <?php
    $all_my_dialogues = Dialogue::get_my_dialoges(
      0, 20, $app
    );
    foreach ($all_my_dialogues as $dialoge) {
      echo $dialoge->get_overview_card(
        $app
      );
    }
    ?>
  </div>
  <?php


  HtmlUtils::footer($app);
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}