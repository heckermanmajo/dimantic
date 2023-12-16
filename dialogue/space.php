<?php
declare(strict_types=1);

use cls\App;
use cls\data\space\Space;
use cls\HtmlUtils;


include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

try {

  App::init_context(basename_file: basename(path: __FILE__));
  $app = App::get();
  $app->init_database();
  [$log, $warn, $err, $todo]
    = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  if (!$app->somebody_logged_in()) {
    header("Location: /index.php");
  }

  $app->handle_action_requests();

  HtmlUtils::head();

  $space = Space::get_by_id(pdo: $app->get_database(), id: (int)$_GET["id"]);

  ?>

  <h2>One Space</h2>

  <?php

  echo $space->getDisplayCard($app);

  echo HtmlUtils::get_markdown_editor_field_for_ajax(
    field_name: "content",
    ajax_end_point_path_from_root: "/request/space/edit_space_content/edit_space_content.php",
    init_text: $space->content,
    extra_json_fields: [
      "id" => $space->id,
    ]
  );
  ?>
  <p>
    <?= $app->markdown_to_html($space->content) ?>
  </p>

  <?php
  HtmlUtils::footer($app);
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}