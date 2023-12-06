<?php
declare(strict_types=1);

use cls\App;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMessage;
use cls\RequestError;

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

try {
  App::init_context(basename(__FILE__));
  $app = App::get();
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  if (!$app->somebody_logged_in()) {
    header("Location: /index.php");
    exit;
  }

  if (!isset($_GET["id"])) {

    # if we directly start a dialogue with a partner (on the profile page)
    # we also get the partner_id in the url
    if (isset($_GET["partner_id"])) {
      $post_data = $_POST;
      $post_data["partner_id"] = $_GET["partner_id"];
    }

    $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/dialogue/create_dialogue/create_dialogue.php"))(
      $app, $post_data ?? $_POST
    );
    if ($result instanceof RequestError) {
      #$create_message_error = $result;
      $result->get_error_card();
      exit();
    }
    else {
      header("Location: /dialogue.php?id=" . $result->id);
      exit();
    }


  }
  $invite_error = null;


  $app->handle_action_requests();

  $dialogue = Dialogue::get_by_id($app->get_database(), (int)$_GET["id"]);

  \cls\HtmlUtils::head();

  ?>
  <br>
  <a class="button w3-margin w3-padding" href="/index.php"> Back to main menu </a>

  <?php
  if ($dialogue->author_id == $app->get_currently_logged_in_account()->id && $dialogue->state == Dialogue::STATE_NOT_YET_STARTED) {
    ?>
    <a class="button w3-margin w3-padding" href="/dialogue.php?id=<?= $dialogue->id ?>&tab=edit_dialoge"> Edit
      Dialogue </a>
    <?php
  }
  ?>
  <?php

  switch ($_GET["tab"] ?? "") {


    case "invite_member":

      if (isset($invite_error) && $invite_error instanceof RequestError) {
        echo $invite_error->get_error_card();
      }
      $all_accounts = \cls\data\account\Account::get_all_accounts(0, 50, $app);
      $all_memberships = $dialogue->get_memberships($app);
      echo "<pre>";
      var_dump($all_memberships);
      echo "</pre>";
      if ($dialogue->get_number_of_memberships($app) >= 2) {
        ?>
        <div style="color: orangered">dialogue is full</div>
        <?php
        foreach ($all_memberships as $membership) {
          ?>
          <?= $membership->get_info_bar($app) ?>
          <?= $membership->get_associated_account($app->get_database())->get_display_card($app) ?>
          <?php
        }
      }
      else {
        foreach ($all_accounts as $account) {

          if ($account->id == $app->get_currently_logged_in_account()->id) {
            continue;
          }
          ?>
          <?= $account->get_display_card($app) ?>
          <form method="post">
            <input type="hidden" name="action" value="invite_account_into_dialogue">
            <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
            <input type="hidden" name="account_id" value="<?= $account->id ?>">
            <button class="button">INVITE TO DIALOGUE: <?= $account->name ?></button>
          </form>
          <?php
        }
      }
      break;


    case "edit_dialoge":
      ?>
      <div class="info-card">

        <p><img src="/res/info.png" width="60"> <b>Edit Dialogue</b></p>
        <ul>
          <li>What topic dou you want to talk about?</li>
          <li>What is the broad goal?</li>
          <li>Do you want some special rules?</li>
        </ul>
      </div>
      <form method="post" class="w3-card w3-margin w3-padding">
        <input type="hidden" name="action" value="edit_dialogue">
        <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
        <!--<label>
        Max number of days to reply.
        <input type="number" name="number_of_days_to_reply" value="<?= $dialogue->number_of_days_to_reply ?>">
      </label>
      <label>
        Message cooldown in hours.
        <input type="number" name="message_cooldown_in_hours" value="<?= $dialogue->message_cooldown_in_hours ?>">
      </label>-->
        <label>
          <textarea rows="10" style="width: 100%" name="content"
                    placeholder="Describe Dialogue"><?= $dialogue->content ?></textarea>
        </label>
        <br><br>
        <button class="button">Edit Dialogue</button>
      </form>

      <?php
      break;


    default:
      if ($dialogue->state == Dialogue::STATE_NOT_YET_STARTED) {
        echo $dialogue->get_overview_card($app);
        ?>
        <pre><?= json_encode($dialogue, JSON_PRETTY_PRINT) ?></pre>
        <pre><?= json_encode($dialogue->get_memberships($app), JSON_PRETTY_PRINT) ?></pre>
        <a class="button" href="/dialogue.php?id=<?= $dialogue->id ?>&tab=invite_member">invite member</a>
        <?php
      }
      elseif ($dialogue->state == Dialogue::STATE_OPEN) {
        echo $dialogue->get_overview_card($app);
        $messages = DialogueMessage::get_all_messages_of_dialogue($app, $dialogue->id);
        $my_membership = $dialogue->get_membership_of_given_account($app, $app->get_currently_logged_in_account()->id);
        ?>
        <div>

        <!--<button class="w3-margin" onclick="FN_TOGGLE('my_notes')">Show/hide My Notes</button>-->

        <div>

          <div class="w3-row">
            <div class="w3-half">

              <div class="w3-card-4 w3-margin">
                <h4 class="menu-header-color" style="cursor: pointer">
                  <span onclick="FN_TOGGLE('my_notes')">My Notes</span>
                  <span onclick="FN_TOGGLE('my_notes_info_box')" style="font-style: normal;"> ℹ️ </span>
                </h4>

                <div id="my_notes_info_box" class="info-card" style="display: none">
                  <h4> Infos about my notes ... </h4>
                </div>

                <div id="my_notes">
                  <?php
                  echo \cls\HtmlUtils::get_markdown_editor_field_for_ajax(
                    field_name: "notes_field",
                    ajax_end_point_path_from_root: "/request/dialogue/update_private_notes/update_private_notes.php",
                    init_text: $my_membership->notes_field,
                    extra_json_fields: [
                      "dialogue_id" => $dialogue->id,
                    ]
                  );
                  ?>
                </div>
                <script>
                  // todo: problem: if the easy mde editor is hidden from the start
                  // todo: it does not load the content
                  // todo: only if the user clicks into the editor the content is loaded
                  $(document).ready(function () {
                    $("#my_notes").hide();
                  });
                </script>
              </div>
            </div>

            <div class="w3-half">
              <div id="create_rule" class="w3-card-4 w3-margin">

                <h4 class="menu-header-color" style="cursor: pointer">
                  <span onclick="FN_TOGGLE('create_rule_draft')">Create Rule</span>
                  <span onclick="FN_TOGGLE('create_rule_draft_info_box')" style="font-style: normal;"> ℹ️ </span>
                </h4>

                <div id="create_rule_draft_info_box" class="info-card" style="display: none">
                  <h4> How to create Rules ... </h4>
                </div>

                <div id="create_rule_draft">
                  <?= \cls\HtmlUtils::get_markdown_editor_field_for_ajax(
                    field_name: "rule_draft_content",
                    ajax_end_point_path_from_root: "/request/dialogue/update_rule_draft/update_rule_draft.php",
                    init_text: $my_membership->rule_draft,
                    extra_json_fields: [
                      "dialogue_id" => $dialogue->id,
                    ]
                  ); ?>

                  <?= ($app->executed_action == "create_rule") ? $app->action_error?->get_error_card() : "" ?>
                  <form method="post">
                    <input type="hidden" name="action" value="create_rule">
                    <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
                    <button class="button">Create Rule</button>
                  </form>

                </div>
                <?php if (!($app->executed_action == "create_rule" && $app->action_error !== null)): ?>
                  <script>
                    // todo: problem: if the easy mde editor is hidden from the start
                    // todo: it does not load the content
                    // todo: only if the user clicks into the editor the content is loaded
                    $(document).ready(function () {
                      $("#create_rule_draft").hide();
                    });
                  </script>
                <?php endif; ?>

              </div> <!-- create_rule -->
            </div>

          </div>

          <div class="w3-card-4 w3-margin">
            <h4 class="menu-header-color" style="cursor: pointer">
              <span onclick="FN_TOGGLE('rules_list')">Rules</span>
              <span onclick="FN_TOGGLE('rules_list_info_box')" style="font-style: normal;"> ℹ️ </span>
            </h4>

            <?php

            if (
              $app->executed_action == "decline_rule"
              || $app->executed_action == "accept_rule"
            ) {
              if ($app->action_error !== null) {
                echo $app->action_error->get_error_card();
              }
            }

            ?>

            <div
              id="rules_list_info_box"
              class="info-card"
              style="display: none">
              <h4> Rules ... </h4>
            </div>

            <div
              id="rules_list"
              <?php if ($app->executed_action != "edit_rule") echo 'style="display: none"' ?>
            >
              <?php
              $rules = $dialogue->get_rules_of_dialogue($app);
              foreach ($rules as $rule) {
                echo $rule->get_display_card($app);
              }
              ?>
            </div>

            <!---
              TODO: Display the rules that are accepted not in the display none part but
              TODO: always for everybody to see.
            -->

          </div>


          <div class="w3-margin w3-padding w3-card-4">
            <input type="hidden" name="action" value="write_message">
            <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
            <label>

              <span onclick="FN_TOGGLE('write_message_info')" style="font-style: normal; cursor: pointer">
                Draft the next message
                ℹ️ </span>
              <div id="write_message_info" class="info-card" style="display: none">
                <h4> How to write a message how stuff works ... </h4>
              </div>
              <?php echo \cls\HtmlUtils::get_markdown_editor_field_for_ajax(
                field_name: "next_message_draft_content",
                ajax_end_point_path_from_root: "/request/dialogue/update_message_draft/update_message_draft.php",
                init_text: $my_membership->next_message_draft,
                extra_json_fields: [
                  "dialogue_id" => $dialogue->id,
                ]
              );
              ?>
            </label>
            <br><br>
            <!-- todo: new request: send message, only display button if it is my turn -->
            <!-- otherwise display as draft -->

            <?= ($app->executed_action == "publish_message_from_draft") ? $app->action_error?->get_error_card() : "" ?>

            <?php if ($dialogue->next_turn_is_my_turn($app)): ?>

              <p class="w3-margin"><b>It is your turn to submit a message.</b></p>
              <span>Publish <b style="color: dodgerblue">§ <?= count($messages) + 1 ?></b> from <b>draft</b></span>

              <form method="post">
                <input type="hidden" name="action" value="publish_message_from_draft">
                <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
                <button
                  class="button"
                  onclick="
                    event.preventDefault();
                    event.stopPropagation();
                    if(confirm('Publish your current draft? NO edit afterwards!')){
                      this.form.submit();
                    }
                  "> Publish your Message
                </button>
              </form>

            <?php else: ?>

              <div class="w3-card w3-margin w3-padding">

                <h6>
                  It is not your turn to write a message.
                  <span onclick="FN_TOGGLE('dialogue_writing_order_info')" style="cursor: pointer"> ℹ️ </span>
                </h6>
                <p>You will get a news entry when your partner has answered.</p>
                <div id="dialogue_writing_order_info" style="display: none" class="info-card">
                  <h4> How does the writing order work? </h4>
                  <p>
                    The writing order is determined by the number of messages you have written.
                    The person with the least messages written is the next one to write a message.
                  </p>
                  <p>
                    If you have written the same number of messages as your partner, the person who has written the last
                    message is the next one to write a message.
                  </p>
                  <p>
                    If you have written the same number of messages as your partner and the last message was written by
                    you, then your partner is the next one to write a message.
                  </p>
                </div>
              </div>


            <?php endif; ?>
          </div>

          <hr>

          <!-- <h2>WRITE MESSAGES!!!</h2>-->
          <?php

          $all_messages_num = count($messages);
          foreach ($messages as $number => $message) {
            $message_number = $all_messages_num - $number;
            echo $message->get_view_card($app, $message_number);
          }
          ?>

        </div>
        <?php
      }
      break;
  }

  \cls\HtmlUtils::footer($app);
}
catch (\Throwable $e) {
  App::dump_logs(t: $e);
}