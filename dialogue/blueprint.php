<?php
declare(strict_types=1);

use cls\App;
use cls\data\conversation_blue_print\ConversationBluePrint;
use cls\data\conversation_blue_print\ProtoRule;
use cls\HtmlUtils;


include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

try {

  App::init_context(basename_file: basename(path: __FILE__));
  $app = App::get();
  $app->init_database();
  [$log, $warn, $err, $todo]
    = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  $log("POST", $_POST);

  if (!$app->somebody_logged_in()) {
    header("Location: /index.php");
    exit();
  }

  $app->handle_action_requests();

  HtmlUtils::head();

  $blueprint = ConversationBluePrint::get_by_id(
    $app->get_database(),
    (int)$_GET["id"]
  );

  ?>
  <a href="/index.php"> ◀️ Back </a>
  <?php

  echo $blueprint->get_card($app);

  # todo: add a form to edit the blueprint content

  ?>
  <h4> Add rules to blueprint </h4>

  <?php

  $proto_rules = ProtoRule::get_array(
    $app->get_database(),
    "SELECT * FROM ProtoRule WHERE blue_print_id = ?",
    [$blueprint->id]
  );


  if ($app->executed_action == "create_proto_rule") {
    if ($app->action_error) {
      echo $app->action_error->get_error_card($app);
    }
    else {
      # success
    }
  }

  ?>

  <form method="post" class="w3-card w3-margin w3-padding">
    <input type="hidden" name="action" value="create_proto_rule">
    <input type="hidden" value="<?= $blueprint->id ?>" name="blue_print_id">
    <?=
    HtmlUtils::get_markdown_editor_field_for_ajax(
      field_name: "content",
      ajax_end_point_path_from_root: HtmlUtils::NO_AJAX_ENDPOINT,
      init_text: "### Describe ONE rule for the conversation you want to have",
      extra_json_fields: []
    )
    ?>
    <div class="w3-margin">
      <button type="submit" class="w3-button w3-blue"> Add rule</button>
    </div>

  </form>

  <h3> Rules </h3>
  <?php
  foreach ($proto_rules as $proto_rule) {
    echo $proto_rule->get_card($app);
  }


  HtmlUtils::footer($app);
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}