<?php

namespace cls\data\dialoge;

use cls\App;
use cls\data\account\Account;
use cls\DataClass;

class DialogueRule extends DataClass {

  var int $dialogue_id = 0;
  var int $post_message_id = 0;
  var string $rule_text = '';
  var int $account_id = 0;
  var int $created_at = 0;
  /**
   * If the rule was initially a proto rule, this field
   * is 0. Proto-Rules mean, they cannot be edited and
   * are accepted by default (and cannot be rejected).
   */
  var int $was_proto_rule = 0;

  /**
   * The difference between rules and summaries
   * is, that a summary just summarizes some important
   * points of the dialogue until now.
   *
   * They are located between the messages. But they
   * can also be accessed at the top, beneath the rules.
   *
   * @var int
   */
  var int $is_summary = 0;

  var int $__rule_order = 0;

  function __construct(array $data_from_db = []) {

    if (isset($data_from_db["created_at"]) and is_string($data_from_db["created_at"])) {
      $data_from_db["created_at"] = time();
    }

    parent::__construct($data_from_db);

  }


  /**
   * @throws \Exception
   */
  function get_current_ratings(): array {
    return DialogueRuleRating::get_array(
      App::get()->get_database(),
      "SELECT * FROM DialogueRuleRating WHERE dialogue_rule_id = ?",
      [$this->id]
    );
  }

  /**
   * @throws \Exception
   */
  function get_dialogue_by_id(): Dialogue {
    return Dialogue::get_by_id(
      App::get()->get_database(),
      $this->dialogue_id
    );
  }

  /**
   *
   * @return string
   * @throws \Exception
   */
  function get_display_card(): string {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    ob_start();

    $author = Account::get_by_id(
      App::get()->get_database(),
      $this->account_id,
    );

    $all_ratings = $this->get_current_ratings();

    $dialogue = $this->get_dialogue_by_id();

    $my_membership = DialogueMembership::get_my_membership_by_dialogue(
      $dialogue->id,
    );

    $my_rating = null;
    $all_have_accepted = true;
    $is_rejected = false;
    /**
     * @var DialogueRuleRating $rating
     */
    foreach ($all_ratings as $rating) {
      if ($rating->account == App::get()->get_currently_logged_in_account()->id) {
        $my_rating = $rating;
      }
      if (
        $rating->rating == DialogueRuleRating::RATING_REJECT
        || $rating->rating == DialogueRuleRating::RATING_PENDING
      ) {
        $all_have_accepted = false;
        if ($rating->rating == DialogueRuleRating::RATING_REJECT) {
          $is_rejected = true;
        }
      }
    }

    # todo: make this check for acceoted memberships
    if (count($all_ratings) == 0) {
      $all_have_accepted = false;
    }

    if ($this->__rule_order == 0) {
      $warn("Rule order not set in db request of rules ");
    }

    $i_am_author = $author->id == App::get()->get_currently_logged_in_account()->id;

    // todo: mark if the rule was declined -> But in theaory the author cvan edit it until it is accepted
    //       need news for declined and news for edited after declined

    $state_color = "orange";
    if ($all_have_accepted) {
      $state_color = "green";
    }
    if ($is_rejected) {
      $state_color = "red";
    }

    # if this is a proto rule, ignore all other stuff
    if ($this->was_proto_rule == 1) {
      $state_color = "blue";
    }

    ?>
    <div class="w3-card w3-padding w3-margin" style="font-size: 90%; border-radius: 20px 20px">
      <b style="font-size: 120%; color:<?= $state_color ?>">
        <i>§§!<?= $this->__rule_order ?></i>
      </b>

      <div class="w3-right" style="color: dimgray">
        <?= $this->created_at ?>
        <?= $author->get_gravtar_profile_image(30) ?>
      </div>

      <?php
      if ($this->was_proto_rule == 1) {
        echo("<span style='color: $state_color'><b>Intial Rule: Automatically Accepted</b></span>");
      }
      else {
        if ($all_have_accepted) {
          echo("<span style='color: $state_color'><b>Rule Accepted</b></span>");
        }
        else {
          if ($is_rejected) {
            echo("<span style='color: $state_color'><b>Rule Rejected</b></span>");
          }
          else {
            echo("<span style='color: $state_color'><b>Rule Pending</b></span>");
          }
        }
      }
      ?>
      <?php if (App::get()->get_currently_logged_in_account()->id == $this->account_id && !$all_have_accepted): ?>
        <button onclick="FN_TOGGLE('edit_or_delete_dialogue_rule_<?= $this->id ?>')" class="button">Edit</button>
      <?php endif; ?>

      <?= App::get()->markdown_to_html($this->rule_text) ?>

      <?php if ($this->was_proto_rule == 0): ?>
        <?php if (!$i_am_author && $my_rating != null
          && $my_rating->rating == DialogueRuleRating::RATING_REJECT): ?>
          <div style="color: <?= $state_color ?>">
            <b>Rule Rejected</b>
            <br>
          </div>
        <?php endif; ?>


        <?php if (!$i_am_author && ($my_rating == null || $my_rating->rating == DialogueRuleRating::RATING_PENDING)): ?>
          <div>
            <form method="post">
              <input type="hidden" name="action" value="accept_rule">
              <input type="hidden" name="dialogue_id" value="<?= $this->dialogue_id ?>">
              <input type="hidden" name="rule_id" value="<?= $this->id ?>">
              <button class="button" type="submit">Accept ✅</button>
            </form>
            <hr>
            <form method="post">
              <input type="hidden" name="action" value="decline_rule">
              <input type="hidden" name="dialogue_id" value="<?= $this->dialogue_id ?>">
              <input type="hidden" name="rule_id" value="<?= $this->id ?>">
              <label>
                <b>Reason for rejection of the rule, so it can be corrected:</b>
                <?php
                echo \cls\HtmlUtils::get_markdown_editor_field_for_ajax(
                  field_name: "reason_text",
                  ajax_end_point_path_from_root: "",
                  init_text: $my_rating?->reason_text ?? "",
                  extra_json_fields: []
                );
                ?>
              </label>

              <button
                class="button"
                type="submit"
                style="color: red">Reject ❌
              </button>
            </form>
          </div>
        <?php endif; ?>

        <?php if (!$i_am_author && $my_rating != null && $my_rating->rating == DialogueRuleRating::RATING_ACCEPT): ?>
          <div style="color: <?= $state_color ?>">
            <b>Rule Accepted</b>
          </div>
        <?php endif; ?>

        <?php foreach ($all_ratings as $rating) {
          if ($rating->rating == DialogueRuleRating::RATING_REJECT) {
            ?>
            <div class="w3-card-4 w3-padding">
              <?= App::get()->markdown_to_html($rating->reason_text) ?>
            </div>
            <?php
          }
          #echo json_encode($rating, JSON_PRETTY_PRINT);
        } ?>


        <?php if ($i_am_author && !$all_have_accepted): ?>
          <?php
          $style = "display: none";
          if (
            (App::get()->executed_action == "edit_rule")
            && $_POST["dialogue_rule_id"] == $this->id
          ):
            $style = "";
          endif;
          ?>
          <div>
            <div class="w3-card-4" id="edit_or_delete_dialogue_rule_<?= $this->id ?>" style="<?= $style ?>">
              <form
                method="post"

              >
                <?= (App::get()->executed_action == "edit_rule"
                  && $_POST["dialogue_rule_id"] == $this->id)
                  ? App::get()->action_error?->get_error_card() : "" ?>
                <input type="hidden" name="action" value="edit_rule">
                <input type="hidden" name="dialogue_rule_id" value="<?= $this->id ?>">
                <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
                <label>
                  Rule-Text:
                  <?php
                  echo \cls\HtmlUtils::get_markdown_editor_field_for_ajax(
                    field_name: "rule_text",
                    ajax_end_point_path_from_root: "",
                    init_text: $this->rule_text,
                    extra_json_fields: [
                      "dialogue_id" => $dialogue->id,
                    ]
                  );
                  ?>
                  <!--<textarea name="rule_text" style="width: 100%; height: 100px"><?= $this->rule_text ?></textarea>-->
                </label>
                <br>
                <button class="button" type="submit">Save changes</button>
              </form>
            </div>
          </div>
        <?php endif; ?> <!-- end of if i am author -->

      <?php endif; ?> <!-- end of if not proto rule -->

    </div>
    <?php
    return ob_get_clean();
  }
}