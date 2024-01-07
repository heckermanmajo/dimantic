<?php
declare(strict_types=1);

use cls\App;
use cls\HtmlUtils;


require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

try {

  App::init_context(basename_file: basename(path: __FILE__));
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

  <h2>Prestige - Marked</h2>

  <h4>Members that want prestige, tasks that want prestige support, ...</h4>

  <pre>
    This marked works over time
    -> but you can boost your post here up by investing prestige
    -> IF it is a good post this allows to get more prestige
  </pre>

  <?php


  HtmlUtils::footer($app);
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}