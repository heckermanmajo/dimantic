<?php
declare(strict_types=1);

use cls\App;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMessage;
use cls\data\dialoge\DialogueMessageSelectionLike;
use cls\HtmlUtils;
use cls\RequestError;

require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

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

  HtmlUtils::head();

  ?>
  <div class="w3-row">
    <div class="w3-col m1 l1 s1 w3-center" style="padding-top: 20px">
      <?= HtmlUtils::get_back_button_html() ?>
    </div>
    <div class="w3-rest">
      <?= $dialogue->get_overview_card(); ?>
    </div>
  </div>
  <?php
  $messages = DialogueMessage::get_all_messages_of_dialogue($dialogue->id);
  $my_membership = $dialogue->get_membership_of_given_account($app->get_currently_logged_in_account()->id);


  $log("Like Percentage: " . $my_membership->like_percentage);
  $log("Number of all chars in messages text: " . $dialogue->get_number_of_all_chars_in_messages_text());
  $log("Absolute amount of all like credits: " . $my_membership->get_absolute_amount_of_all_possible_like_credits());
  $log("Used like credits of this account: " . DialogueMessageSelectionLike::get_all_used_like_credits_per_person_per_dialogue(
      $dialogue->id,
      $app->get_currently_logged_in_account()->id
    )
  );
  $log("Free like credits of this account: " . $my_membership->get_absolute_amount_of_FREE_like_credits());

  ?>

  <div>  <!-- page start -->

    <div class="w3-row">  <!-- START ROW -->
      <div class="w3-half"> <!-- COL 1 -->
        <!-----------------------------------------
        DIALOGUE MESSAGES
        ------------------------------------------->
        <div class="w3-margin w3-padding w3-card-4">

          <input type="hidden" name="action" value="write_message">
          <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
          <label>
        <span onclick="FN_TOGGLE('write_message_info')" style="font-style: normal; cursor: pointer">
          Draft the next messageℹ️
        </span>
            <div id="write_message_info" class="info-card" style="display: none">
              <h4> How to write a message how stuff works ... </h4>
            </div>
            <?php echo HtmlUtils::get_markdown_editor_field_for_ajax(
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

          <?= ($app->executed_action == "publish_message_from_draft") ? $app->action_error?->get_error_card() : "" ?>

          <?php if ($dialogue->next_turn_is_my_turn()): ?>

            <p class="w3-margin"><b>It is your turn to submit a message.</b></p>

            <span>
          Publish
          <b style="color: dodgerblue">§ <?= count($messages) + 1 ?></b>
          from <b>draft</b>
        </span>

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

          <?php else: ?> <!-- if it is not my turn -->

            <!-- IF IT IS NOT MY TURN -->
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

            </div> <!-- end it's my turn -->

          <?php endif; ?> <!-- if it is not my turn -->

        </div> <!-- end dialogue messages -->

        <hr>

        <?php

        $all_messages_num = count($messages);
        foreach ($messages as $number => $message) {
          $message_number = $all_messages_num - $number;
          echo $message->get_view_card(
            $message_number,
            number_of_free_like_credits: $my_membership->get_absolute_amount_of_FREE_like_credits()
          );
        }
        ?>
      </div> <!-- end COL 1 -->

      <div class="w3-half"> <!-- COL 2 -->


        <!-------------------------------------------------------------------
             RULES
           -------------------------------------------------------------------->
        <div class="w3-margin">

          <h2 class="menu-header-color" style="cursor: pointer">
            <span onclick="FN_TOGGLE('rules_list')">Rules</span>
            <span onclick="FN_TOGGLE('rules_list_info_box')" style="font-style: normal;"> ℹ️ </span>
          </h2>

          <div
            id="rules_list_info_box"
            class="info-card"
            style="display: none">
            <h4> Rules ... </h4>


          </div>


          <!---------------------------------
            Create Rule
            --------------------------------->
          <div id="create_rule" class="w3-card-4 w3-margin">

            <h4 class="menu-header-color" style="cursor: pointer">
              <span onclick="FN_TOGGLE('create_rule_draft')">Create Rule</span>
              <span onclick="FN_TOGGLE('create_rule_draft_info_box')" style="font-style: normal;"> ℹ️ </span>
            </h4>

            <div id="create_rule_draft_info_box" class="info-card" style="display: none">
              <h4> How to create Rules ... </h4>
            </div>


            <!---------------------------------
              Create Rule draft form
              --------------------------------->
            <div id="create_rule_draft">

              <?= ($app->executed_action == "create_rule") ? $app->action_error?->get_error_card() : "" ?>

              <form method="post">
                <?= HtmlUtils::get_markdown_editor_field_for_ajax(
                  field_name: "rule_draft_content",
                  ajax_end_point_path_from_root: "/request/dialogue/update_rule_draft/update_rule_draft.php",
                  init_text: $my_membership->rule_draft,
                  extra_json_fields: [
                    "dialogue_id" => $dialogue->id,
                  ]
                ); ?>
                <input type="hidden" name="action" value="create_rule">
                <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
                <button class="button">Create Rule</button>
              </form>

            </div> <!-- end create_rule draft -->

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

          </div> <!-- End create rule --->


          <!---------------------------------
            List of the rules
            --------------------------------->
          <div
            id="rules_list"
            <?php #if ($app->executed_action != "edit_rule") echo 'style="display: none"' ?>
          >
            <?php
            $rules = $dialogue->get_rules_of_dialogue();
            foreach ($rules as $rule) {
              echo $rule->get_display_card();
              if (
                $app->executed_action == "decline_rule"
                || $app->executed_action == "accept_rule"
              ) {
                if ($app->action_error !== null) {
                  if ($_POST["rule_id"] == $rule->id) {
                    echo $app->action_error->get_error_card(App::get());
                  }
                }
              }
            }
            ?>
          </div> <!-- end rules_list -->

          <!---
            TODO: Display the rules that are accepted not in the display none part but
            TODO: always for everybody to see.
          -->

        </div> <!-- RULES -->

        <!------------------------------------------------------
        NOTES
        ------------------------------------------------------->
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
            echo HtmlUtils::get_markdown_editor_field_for_ajax(
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
        </div> <!-- end NOTES -->

      </div> <!-- end COL 2 -->

    </div> <!-- END ROW -->


    <hr>




  </div> <!-- page end -->

  <?php

  HtmlUtils::footer();
}
catch (\Throwable $e) {
  App::dump_logs(t: $e);
}