<?php
declare(strict_types=1);

namespace cls\data\dialoge;

use cls\App;
use cls\data\account\Account;
use cls\DataClass;
use cls\HtmlUtils;
use cls\lib\Parsedown;
use cls\RequestError;
use Exception;

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
  
  /**
   * @throws Exception
   */
  function get_dialogue(): Dialogue {
    return Dialogue::get_one(
      pdo: App::get()->get_database(),
      sql: "SELECT * FROM `Dialogue` WHERE `id` = ?",
      params: [$this->dialogue_id]
    );
  }

  function get_preview_of_content(): string {
    // todo:  use interpreter to return first 3 lines
    return $this->content;
  }
  
  /**
   * @return array<DialogueMessageComment>
   * @throws Exception
   */
  function get_message_comments(): array {
    return DialogueMessageComment::get_array(
      pdo: App::get()->get_database(),
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
   * @param int $dialogue_id
   * @return array<DialogueMessage>
   * @throws Exception
   */
  static function get_all_messages_of_dialogue(int $dialogue_id): array {
    return DialogueMessage::get_array(
      pdo: App::get()->get_database(),
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

  /**
   * @throws Exception
   */
  function get_content_where_liked_stuff_is_underlined(): string {
    # todo: this only works for two user conversations
    # todo: this only works if like sections are not overlapping
    $content = App::get()->markdown_to_html($this->content);
    $all_likes = DialogueMessageSelectionLike::get_all_like_selections_of_message(
      dialogue_message_id: $this->id
    );

    foreach ($all_likes as $like) {
      $content = str_replace(
        $like->selection,
        "<u style='color: #00ffe9'>" . $like->selection . "</u>",
        $content
      );
    }

    return $content;

  }

  /**
   * Returns the HTML-Card for displaying a message within a dialogue.
   *
   * -> also contains the card for commenting the message
   * -> also contains the form-fields for liking a selection of the message
   *
   * @param int $message_number
   * @param int $number_of_free_like_credits
   * @return string
   * @throws Exception
   */
  function get_view_card(
    int $message_number = 0,
    int $number_of_free_like_credits = 0,
  ): string {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    ob_start();

    $dialogue = $this->get_dialogue();

    $membership = $dialogue->get_membership_of_given_account(App::get()->get_currently_logged_in_account()->id);

    $author = Account::get_by_id(App::get()->get_database(), $this->account_id);

    $unique_css_prefix = hash("sha256", (string)$this->id);

    # if I am member and active
    # I want my messages on the left whatever
    if ($membership && $membership->state == DialogueMembership::STATE_ACTIVE) {
      if ($this->account_id == App::get()->get_currently_logged_in_account()->id) {
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

      <!-- Message Header bar -->
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


      <!-- Message Content -->
      <div
        onmousemove="FN_HANDLE_UPDATE_TEXT_SELECTION(<?= $this->id ?>, <?= $number_of_free_like_credits ?>)"
      ><?php echo $this->get_content_where_liked_stuff_is_underlined()?></div>


      <!--<pre><?= json_encode($this, JSON_PRETTY_PRINT) ?></pre>-->


      <?php
      # comment section
      $all_comments = $this->get_message_comments();

      # if the creation of a comment from a selection failed
      $error_occurred_by_creating_comment_for_this_message = (
        App::get()->executed_action == "create_comment_from_selection"
        && App::get()->action_error !== null
        && $_POST["dialogue_message_id"] == $this->id
      );

      if ($error_occurred_by_creating_comment_for_this_message) {
        # if error occurred, we want to display the form and put the values back in
        $selection = $_POST["selection"];
        $comment_text = $_POST["comment_text"];
        $style = "display:block;";
      }
      else {
        $selection = "";
        $comment_text = "";
        $style = "display:none;";
      }

      if ($error_occurred_by_creating_comment_for_this_message) {
        echo App::get()->action_error?->get_error_card();
      }

      ?>

      <!-- Create Comment form -->
      <div
        class="w3-card-4 w3-margin w3-padding"
        id="create_comment_from_selection_<?= $this->id ?>"
        style="<?= $style ?>"
      >

        <!-- header of comment form -->
        <div class="w3-right">
          <button
            class="delete-button"
            onclick="FN_TOGGLE('create_comment_from_selection_<?= $this->id ?>')"
          >
            X CLOSE FORM
          </button>
        </div>

        <br>
        <hr>


        <div><!-- comment form-info-box-1 -->

          <!-- The selected text -->
          <pre id="create_comment_from_selection_<?= $this->id ?>_span"><?= $selection ?></pre>

          <!--
          Create Like selection form.
          -->
          <form
            method="post"
            style="display: inline-block"
            id="like_form_message_<?= $this->id ?>"
          >
            <b><span id="len_of_selected_text_in_like_form_<?= $this->id ?>"></span></b>
            Chars Selected
            <input type="hidden" name="action" value="create_selection_like">
            <input type="hidden" name="dialogue_message_id" value="<?= $this->id ?>">
            <input type="hidden" id="like_selection_<?= $this->id ?>_hidden_input" name="liked_selection">

            <button class="button" style="color: forestgreen; border-color: #4CAF50"> Like the passage ❤️‍🔥</button>
          </form>


          <div
            id="like_error_div_<?= $this->id ?>"
            style="display: none" class="info-card">
            <p>⚠️ Selected Text is too long, cant like it</p>
          </div>

          (this like would cost <span id="cost_of_like_of_selected_text_<?= $this->id ?>">XXX</span> of
          your <?= $membership->get_absolute_amount_of_FREE_like_credits() ?> free like points)
          <span style="cursor: pointer" onclick="FN_TOGGLE('<?= $unique_css_prefix ?>_info_about_likes')">ℹ️</span>


        </div><!-- comment form-info-box-1 -->

        <hr>

        <div> <!-- comment form-info-box-2 -->

          <b>Write comment below </b>
          <span style="cursor: pointer" onclick="FN_TOGGLE('<?= $unique_css_prefix ?>_info_about_comments')">ℹ️</span>

        </div> <!-- end comment form-info-box-2 -->


        <!-- INFO-CARD about comments -->
        <div
          onclick="FN_TOGGLE('<?= $unique_css_prefix ?>_info_about_comments')"
          id="<?= $unique_css_prefix ?>_info_about_comments" style="display:none" class="info-card">

          Information about
          comments
        </div>


        <!-- INFO-CARD about likes -->
        <div
          onclick="FN_TOGGLE('<?= $unique_css_prefix ?>_info_about_likes')"
          id="<?= $unique_css_prefix ?>_info_about_likes" style="display:none" class="info-card">
          Information about likes
        </div>


        <!-- The form for creating a comment -->
        <form method="post">
          <?php
          if ($error_occurred_by_creating_comment_for_this_message) {
            echo App::get()->action_error?->get_error_card();
          }
          ?>
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
          #       All comments of a message can be edited UNTIL the message has received
          #       an answer message
          echo HtmlUtils::get_markdown_editor_field_for_ajax(
            field_name: "comment_text",
            ajax_end_point_path_from_root: "",
            init_text: "",
            extra_json_fields: [
              "dialogue_id" => $dialogue->id,
            ]
          );

          ?>
          <br>
          <button class="button">Create Comment</button>
        </form>

        <br><br>
      </div>

      <!-- The comments of the message as a list -->
      <div id="comments_of_message_<?= $this->id ?>">
        <?php
        foreach ($all_comments as $num => $comment) {
          # the comment number is used to reference the comment in text (§4.1) f.e.
          echo $comment->get_display_card( $message_number, $num);
        }
        ?>
      </div>

    </div>

    <!-- The Form for creating a comment from a text selection -->
    <?php
    return ob_get_clean();
  }
}