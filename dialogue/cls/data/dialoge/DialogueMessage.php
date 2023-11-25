<?php
declare(strict_types=1);

namespace cls\data\dialoge;

use cls\App;
use cls\data\account\Account;
use cls\DataClass;
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

  function get_view_card(App $app): string {
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

    # if i am member and active
    # i want my messages on the left whatever
    if ($mem && $mem->state == DialogueMembership::STATE_ACTIVE) {
      if ($this->account_id == $app->get_currently_logged_in_account()->id) {
        $style = "margin-right: 20% !important;";
      }
      else {
        $style = "margin-left: 20% !important; border-color: #69ff7a";
      }
    }
    else if ($dialogue->author_id == $this->account_id) {
      # if I am not member, i wants the authors messages on the left
      $style = "margin-right: 20% !important;";
    }
    else {
      $style = "margin-left: 20% !important; border-color: #69ff7a";
    }
    ?>
    <div class="w3-card-4 w3-margin w3-padding" style="<?= $style ?>">
      <div>
        <?= $author->get_gravtar_profile_image(size: 18) ?>
        <small><?= $this->create_date ?></small>
      </div>
      <div
        onmousemove="FN_HANDLE_UPDATE_TEXT_SELECTION(<?= $this->id ?>)"
      ><?= $app->markdown_to_html(markdown: $this->content) ?></div>
      <!--<pre><?= json_encode($this, JSON_PRETTY_PRINT) ?></pre>-->
      <?php
      $all_comments = $this->get_message_comments($app);
      if (count($all_comments) != 0):
        ?>
        <button
          class="button"
          onclick="FN_TOGGLE('comments_of_message_<?= $this->id ?>')"
        >
          Show Comments (<?= count($all_comments) ?>)
        </button>
      <?php
      endif;
      ?>
      <div
        id="comments_of_message_<?= $this->id ?>" style="display: none">
        <?php

        foreach ($all_comments as $comment) {
          echo $comment->get_display_card($app);
        }
        ?>
      </div>
    </div>

    <!-- The Form for creating a comment from a text selection -->
    <?php
    if (isset($create_comment_from_selection_error) and $_POST["dialogue_message_id"] == $this->id){
      $selection = $_POST["selection"];
      $comment_text = $_POST["comment_text"];
      $style = "display:block;";
    }
    else{
      $selection = "";
      $comment_text = "";
      $style = "display:none;";
    }
    ?>
    <div
      class="w3-card-4 w3-margin w3-padding"
      id="create_comment_from_selection_<?= $this->id ?>"
      style="<?= $style ?>"
    >
      <p>Write comment for following text-selection: </p>
      <pre id="create_comment_from_selection_<?= $this->id ?>_span"><?=$selection?></pre>
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
          value="<?=$selection?>">
        <textarea
          name="comment_text"
          style="width: 100%"
          id="create_comment_from_selection_<?= $this->id ?>_textarea"><?=$comment_text?></textarea>
        <br>
        <button class="button">Create Comment</button>
      </form>
      <br><br>
      <button
        class="delete-button"
        onclick="FN_TOGGLE('create_comment_from_selection_<?= $this->id ?>')"
      >
        X CLOSE FORM
      </button>
    </div>
    <?php
    return ob_get_clean();
  }
}