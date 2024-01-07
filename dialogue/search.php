<?php
declare(strict_types=1);

use cls\App;
use cls\data\dialoge\Dialogue;
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
  <div class="w3-margin w3-padding">
    <input type="text" value="search stuff">
  </div>
  <?php


  HtmlUtils::footer($app);
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}