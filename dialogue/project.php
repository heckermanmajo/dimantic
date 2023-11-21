<?php

use cls\App;
use cls\HtmlUtils;

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
try {
  App::init_context(basename(__FILE__));
  $app = App::get();
  $app->init_database();

  HtmlUtils::head();
  HtmlUtils::main_header();
  ?>
  <br>
  <a class="button w3-margin w3-padding" href="/index.php"> ZURÜCK </a>
  <div class="w3-margin">
    <h2>Project information </h2>
    <pre>

    - how to participate in the project (open source coding)
    - how to participate in the project (conceptually / feedback)
    - how to participate in the project (spreading the word)

    The mission of the project.

    Some of the base content to understand the basic mind space we are in.
    -> Video of explanation of the project.

    ---

    Current code explanation-videos of basic code structure and workings (links to YT)


  </pre>
  </div>
  <?php
  HtmlUtils::footer($app);
}
catch (\Throwable $e) {
  App::dump_logs(t: $e);
}