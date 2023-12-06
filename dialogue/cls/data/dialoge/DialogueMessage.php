<?php
declare(strict_types=1);

namespace cls\data\dialoge;

use cls\App;
use cls\data\account\Account;
use cls\DataClass;
use cls\HtmlUtils;
use cls\lib\Parsedown;
use cls\RequestError;

class  DialogueMessage extends DataClass {
  ###########################################################################
  #                                                                         #
  #  Properties & Property-functions                                        #
  #                                                                         #
  ###########################################################################

  var string $content = '';
  var int $dialogue_id = 0;
  var int $account_id = 0;
  var string $create_date = '';

  #################################
  ###### Joined Values      #######
  #################################

  #################################
  ###### Property-functions #######
  #################################

  function get_dialogue(App $app): Dialogue {
    return Dialogue::get_one(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `Dialogue` WHERE `id` = ?",
      params: [$this->dialogue_id]
    );
  }

  function get_preview_of_content(): string {
    // todo:  use interpreter to return first 3 lines
    return $this->content;
  }

  /**
   * @param App $app
   * @return array<DialogueMessageComment>
   */
  function get_message_comments(App $app): array {
    return DialogueMessageComment::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `DialogueMessageComment` WHERE `dialogue_message_id` = ?",
      params: [$this->id]
    );
  }

  ###########################################################################
  #                                                                         #
  #  Model-Queries                                                          #
  #                                                                         #
  ###########################################################################

  /**
   * @param App $app
   * @param int $dialogue_id
   * @return array<DialogueMessage>
   */
  static function get_all_messages_of_dialogue(App $app, int $dialogue_id): array {
    return DialogueMessage::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `DialogueMessage` WHERE `dialogue_id` = ? ORDER BY id DESC",
      params: [$dialogue_id]
    );
  }

  ###########################################################################
  #                                                                         #
  #  Logic & Controller                                                     #
  #                                                                         #
  ###########################################################################

  ###########################################################################
  #                                                                         #
  #  Views                                                                  #
  #                                                                         #
  ###########################################################################

  function get_view_card(App $app, int $message_number = 0): string {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    /**
     * This field is possibly set in the handler.php
     * It is the return value of the create_comment_from_selection.php
     * @see /handler.php
     * @see /request/dialogue/create_comment_from_selection/create_comment_from_selection.php
     * @var $dialogue RequestError|undefined - can also be undefined
     */
    global $create_comment_from_selection_error;

    ob_start();

    $dialogue = $this->get_dialogue($app);

    $mem = $dialogue->get_membership_of_given_account($app, $app->get_currently_logged_in_account()->id);

    $author = Account::get_by_id($app->get_database(), $this->account_id);

    $unique_css_prefix = hash("sha256", (string)$this->id);

    # if i am member and active
    # i want my messages on the left whatever
    if ($mem && $mem->state == DialogueMembership::STATE_ACTIVE) {
      if ($this->account_id == $app->get_currently_logged_in_account()->id) {
        $style = "margin-right: 20% !important;border-color: lightblue;border-width: 2px;";
      }
      else {
        $style = "margin-left: 20% !important; border-color: green;border-width: 2px;";
      }
    }
    else if ($dialogue->author_id == $this->account_id) {
      # if I am not member, i wants the authors messages on the left
      $style = "margin-right: 20% !important;border-color: lightblue;border-width: 2px;";
    }
    else {
      $style = "margin-left: 20% !important; border-color: green; border-width: 2px;";
    }
    ?>
    <div class="w3-card-4 w3-margin w3-padding" style="<?= $style ?>">
      <div>

        <b style="font-size: 20px; color: dodgerblue;">
          § <?= $message_number ?>
        </b>
        &nbsp;&nbsp;&nbsp;
        <div class="w3-right">
          <small style="color: #818181"><?= $this->create_date ?></small>
          <?= $author->get_gravtar_profile_image(size: 30) ?>
        </div>

      </div>
      <div
        onmousemove="FN_HANDLE_UPDATE_TEXT_SELECTION(<?= $this->id ?>)"
      ><?php
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);
        $text = $parsedown->parse($this->content);
        echo $text;
        ?></div>
      <!--<pre><?= json_encode($this, JSON_PRETTY_PRINT) ?></pre>-->
      <?php
      $all_comments = $this->get_message_comments($app);
      if (count($all_comments) != 0):
        ?>
        <!-- <button
          class="button"
          onclick="FN_TOGGLE('comments_of_message_<?= $this->id ?>')"
        >
          Show Comments (<?= count($all_comments) ?>)
        </button>-->
      <?php
      endif;

      if (isset($create_comment_from_selection_error) and $_POST["dialogue_message_id"] == $this->id) {
        $selection = $_POST["selection"];
        $comment_text = $_POST["comment_text"];
        $style = "display:block;";
      }
      else {
        $selection = "";
        $comment_text = "";
        $style = "display:none;";
      }
      ?>
      <?= ($app->executed_action == "create_comment_from_selection"
        && $_POST["dialogue_message_id"] == $this->id) ? $app->action_error?->get_error_card() : ""; ?>
      <div
        class="w3-card-4 w3-margin w3-padding"
        id="create_comment_from_selection_<?= $this->id ?>"
        style="<?= $style ?>"
      >
        <div class="w3-right">
          <button
            class="delete-button"
            onclick="FN_TOGGLE('create_comment_from_selection_<?= $this->id ?>')"
          >
            X CLOSE FORM
          </button>
        </div>
        <p>
          <b>Write comment below </b>
          <span style="cursor: pointer" onclick="FN_TOGGLE('<?=$unique_css_prefix?>_info_about_comments')">ℹ️</span>
          for following text-selection
          <b>OR</b>
          <button class="button" style="color: forestgreen; border-color: #4CAF50"> Like the passage ❤️‍🔥</button>
          <span style="cursor: pointer"  onclick="FN_TOGGLE('<?=$unique_css_prefix?>_info_about_likes')">ℹ️</span>
          <br>
          (this like would cost XXX of your XXX like points)
          <span style="cursor: pointer"  onclick="FN_TOGGLE('<?=$unique_css_prefix?>_info_about_likes')">ℹ️</span>
        </p>
        <div
          onclick="FN_TOGGLE('<?=$unique_css_prefix?>_info_about_comments')"
          id="<?=$unique_css_prefix?>_info_about_comments" style="display:none" class="info-card"> Information about comments </div>
        <div
          onclick="FN_TOGGLE('<?=$unique_css_prefix?>_info_about_likes')"
          id="<?=$unique_css_prefix?>_info_about_likes" style="display:none" class="info-card"> Information about likes </div>
        <pre>
        This like costs XXX of your XXX like points.
        -> if it costs to much like points, you can not like it.
        -> This needs to be handled by an JS-Call.
        -> Help button to explain the like points.
        -> Hide the like button, once there is content in the comment-textarea field.
      </pre>
        <pre id="create_comment_from_selection_<?= $this->id ?>_span"><?= $selection ?></pre>
        <form method="post">
          <?= ($create_comment_from_selection_error ?? null)?->get_error_card() ?>
          <input name="dialogue_message_id" type="hidden" value="<?= $this->id ?>">
          <input
            type="hidden"
            name="action"
            value="create_comment_from_selection">
          <input
            type="hidden"
            id="create_comment_from_selection_<?= $this->id ?>_hidden_input"
            name="selection"
            value="<?= $selection ?>">
          <?php
          # todo: make comments editable until i have written my message ...
          echo \cls\HtmlUtils::get_markdown_editor_field_for_ajax(
            field_name: "comment_text",
            ajax_end_point_path_from_root: "",
            init_text: "",
            extra_json_fields: [
              "dialogue_id" => $dialogue->id,
            ]
          );

          ?>
          <!--<textarea
          name="comment_text"
          id="create_comment_from_selection_<?= $this->id ?>_textarea"
          style="width: 100%"
          id="create_comment_from_selection_<?= $this->id ?>_textarea"><?= $comment_text ?></textarea>-->
          <br>
          <button class="button">Create Comment</button>
        </form>
        <br><br>
      </div>
      <div
        id="comments_of_message_<?= $this->id ?>"> <!-- style="display: none">-->
        <?php

        foreach ($all_comments as $num => $comment) {
          echo $comment->get_display_card($app, $message_number, $num);
        }
        ?>
      </div>
    </div>

    <!-- The Form for creating a comment from a text selection -->
    <?php
    return ob_get_clean();
  }
}