<?php


use cls\App;
use cls\data\conversation_blue_print\ConversationBluePrint;
use cls\data\space\Space;
use cls\HtmlUtils;


return function (Space $space, App $app): string {
  ob_start();

  if($app->executed_action == "create_conversation_blueprint") {
    $app->action_error?->get_error_card($app);
    # if no error, redirect to the blueprint page
    if($app->action_error == null) {
      if($app->success_result instanceof ConversationBluePrint) {
        ob_clean();
        header("Location: /blueprint.php?id={$app->success_result->id}");
      }
    }
  }

  ?>

  <br><br>
  <form method="post" class="w3-card">
    <h4 class="w3-padding">Create a new Conversation Blueprint <span style="cursor: pointer" onclick="FN_TOGGLE('blue_print_info')">ℹ️</span> </h4>
    <div style="display: none" id="blue_print_info" class="info-card">
      <p>Explanation of blueprints ... </p>
    </div>
    <input type="hidden" name="action" value="create_conversation_blueprint">
    <input type="hidden" name="space_id" value="<?= $space->id ?>">
    <?=
    HtmlUtils::get_markdown_editor_field_for_ajax(
      field_name: "description",
      ajax_end_point_path_from_root: HtmlUtils::NO_AJAX_ENDPOINT,
      init_text: "### Description of the conversation you want to have
Describe the topic you want to talk about.
        ",
      extra_json_fields: []
    )
    ?>
    <div class="info-card">
      ℹ️ You can add rules to this blueprint, once you have created it.
      <br>
      It will be not published after creation, but will be a draft until you publish it.
    </div>
    <div class="w3-margin">
      <button class="w3-button w3-green">Create Conversation Blueprint</button>
    </div>
  </form>


  <?php
  return ob_get_clean();
};