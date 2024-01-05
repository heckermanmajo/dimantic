<?php
declare(strict_types=1);

use cls\App;
use cls\HtmlUtils;


include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

try {

  App::init_context(basename_file: basename(path:__FILE__));
  $app = App::get();
  $app->init_database();
  [$log, $warn, $err, $todo]
    = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  if (!$app->somebody_logged_in()) {
    # header("Location: /index.php");
  }

  $app->handle_action_requests();

  HtmlUtils::head();
  HtmlUtils::main_header();
  ?>
  <h3>
    Feed -> Gitterfeed
  </h3>
  <?php
  HtmlUtils::footer($app);
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}