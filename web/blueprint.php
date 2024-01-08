<?php
declare(strict_types=1);

use cls\App;
use cls\data\conversation_blue_print\ConversationBluePrint;
use cls\data\conversation_blue_print\Lobby;
use cls\data\conversation_blue_print\ProtoRule;
use cls\HtmlUtils;


require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

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

  if (
    !$blueprint->user_is_allowed_to_see_blueprint(
      $app->get_currently_logged_in_account()->id
    )
  ) {
    echo "You are not allowed to see this blueprint.";
    goto END_OF_PAGE;
  }

  $current_user_is_blue_print_author = $blueprint->author_id === $app->get_currently_logged_in_account()->id;

  ?>
  <a href="/index.php"> ◀️ Back </a>
  <?php

  echo $blueprint->get_display_card();

  # todo: add a form to edit the blueprint content

  $lobbies = Lobby::get_lobbies_of_conversation_blueprint(
    $blueprint->id
  );

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


  if (
    $blueprint->user_is_allowed_to_edit_blueprint(
      $app->get_currently_logged_in_account()->id
    )
  ):
    ?>
    <h4> Edit Blueprint </h4>
    <?=
    HtmlUtils::get_markdown_editor_field_for_ajax(
      field_name: "description",
      ajax_end_point_path_from_root: "/request/blueprint/edit_conversation_blueprint_description/edit_conversation_blueprint_description.php",
      init_text: $blueprint->description,
      extra_json_fields: [
        "blue_print_id" => $blueprint->id,
      ]
    )
    ?>

    <hr>
    <div class="sketch-card w3-margin">
      <script>
        $(document).ready(function () {
          $("#create_rule_form").hide();
        });
      </script>

      <h3> Rules </h3>

      <button class="sketch-button" onclick="FN_TOGGLE('create_rule_form')"> Add rules to blueprint </button>
      <form method="post" id="create_rule_form">
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
          <button type="submit" class="sketch-button"> Add rule </button>
        </div>

      </form>
    </div>

  <?php endif; ?>

  <?php

  foreach ($proto_rules as $proto_rule) {
    echo $proto_rule->get_card();
  }

  if ($app->executed_action == "edit_proto_rule") {
    if ($app->action_error) {
      echo $app->action_error->get_error_card($app);
    }
    else {
      # success
    }
  }

  if (
    $blueprint->user_is_allowed_to_edit_blueprint(
      $app->get_currently_logged_in_account()->id
    )
  ):
    ?>
    <?php if ($blueprint->published == 0): ?>
    <h4>Publish The blueprint</h4>
    <pre>
    - you need to create at least one lobby to publish
  </pre>
    <form method="post">
      <input type="hidden" name="action" value="publish_blueprint">
      <input type="hidden" value="<?= $blueprint->id ?>" name="blue_print_id">
      <button type="submit" class="w3-button w3-blue"> Publish</button>
    </form>

  <?php else: ?>
    <h4>Unpublish The blueprint</h4>
    <form method="post">
      <input type="hidden" name="action" value="unpublish_blueprint">
      <input type="hidden" value="<?= $blueprint->id ?>" name="blue_print_id">
      <button type="submit" class="w3-button w3-blue"> Unpublish</button>
    </form>
  <?php endif; ?>
    <?php
    if ($app->executed_action == "publish_blueprint") {
      if ($app->action_error) {
        echo $app->action_error->get_error_card($app);
      }
      else {
        ?>
        <div class="sketch-card" style="border-color: forestgreen; ">
          <div class="w3-container">
            <h3 style="color: green"> The blueprint has been published </h3>
          </div>
        </div>
        <?php
      }
    }
    if ($app->executed_action == "unpublish_blueprint") {
      if ($app->action_error) {
        echo $app->action_error->get_error_card($app);
      }
      else {
        ?>
        <div class="sketch-card" style="border-color: forestgreen">
          <div class="w3-container">
            <h3 style="color: green"> The blueprint has been unpublished </h3>
          </div>
        </div>
        <?php
      }
    }

  endif;
  ?>


  <?php if (
    $blueprint->user_is_allowed_to_create_lobby(
      $app->get_currently_logged_in_account()->id
    )
  ): ?>
    <h3> Lobbies </h3>
    <form method="post" class="w3-card w3-margin w3-padding">
      <h5> Create Lobby </h5>
      <input type="hidden" name="action" value="create_lobby">
      <input type="hidden" value="<?= $blueprint->id ?>" name="blue_print_id">

      <div class="w3-margin">
        <button type="submit" class="w3-button w3-blue"> Create Lobby</button>
      </div>
    </form>
  <?php endif; ?>
  <?php

  foreach ($lobbies as $lobby) {
    echo $lobby->display_card();
  }


  END_OF_PAGE:
  HtmlUtils::footer();

}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}