<?php

namespace cls\data\dialoge;

use cls\App;
use cls\data\account\Account;
use cls\DataClass;
use cls\RequestError;

class DialogueRule extends DataClass {
  var int $dialogue_id = 0;
  var int $post_message_id = 0;
  var string $rule_text = '';
  var int $account_id = 0;

  var int $__rule_order = 0;

  function get_current_ratings(App $app): array {
    return DialogueRuleRating::get_array(
      $app->get_database(),
      "SELECT * FROM dialogue_rule_rating WHERE dialogue_rule_id = ?",
      [$this->id]
    );
  }

  function get_dialogue_by_id(App $app): Dialogue {
    return Dialogue::get_by_id(
      $app->get_database(),
      $this->dialogue_id
    );
  }

  /**
   *
   * @return string
   */
  function get_display_card(App $app): string {
    /**
     * @var $delete_dialogue_rule_error RequestError|null
     */
    global $delete_dialogue_rule_error;
    /**
     * @var $edit_dialogue_rule_error RequestError|null
     */
    global $edit_dialogue_rule_error;
    ob_start();

    $author = Account::get_by_id(
      $app->get_database(),
      $this->account_id,
    );

    $all_ratings = $this->get_current_ratings($app);

    $dialogue = $this->get_dialogue_by_id($app);

    $my_membership = DialogueMembership::get_my_membership_by_dialogue(
      $dialogue->id,
      $app
    );

    $my_rating = null;
    $all_have_accepted = true;
    foreach ($all_ratings as $rating) {
      if ($rating->account_id == $app->get_currently_logged_in_account()->id) {
        $my_rating = $rating;
      }
      if ($rating->rating == DialogueRuleRating::RATING_REJECT || $rating->rating == DialogueRuleRating::RATING_PENDING) {
        $all_have_accepted = false;
      }
    }

    if ($this->__rule_order == 0) {
      throw new \Exception("Rule order not set in db request of rules ");
    }

    $i_am_author = $author->id == $app->get_currently_logged_in_account()->id;

    // todo: mark if the rule was declined -> But in theaory the author cvan edit it until it is accepted
    //       need news for declined and news for edited after declined

    ?>
    <div class="w3-card" style="font-size: 90%">
      <div>
        <?= $author->get_gravtar_profile_image(20) ?>
        <small><?= $author->name ?></small>
      </div>
      <b style="font-size: 120%">§<?= $this->__rule_order ?></b>
      <pre style="padding-left: 5px; margin: 0"><?= $this->rule_text ?></pre>
      <?php if ($i_am_author && !$all_have_accepted): ?>
        <?php
        $style = "display: none";
        if (
          (isset($delete_dialogue_rule_error) || isset($edit_dialogue_rule_error))
          && $_POST["dialogue_rule_id"] == $this->id
        ):
          $style = "";
        endif;
        ?>
        <div>
          <button onclick="FN_TOGGLE('edit_or_delete_dialogue_rule_<?= $this->id ?>')" class="button">Edit</button>
          <br>
          <div class="w3-card-4" id="edit_or_delete_dialogue_rule_<?= $this->id ?>">
            <form
              method="post"
              style="<?= $style ?>"
            >
              <?= ($edit_dialogue_rule_error ?? null)?->get_error_card() ?>
              <input type="hidden" name="action" value="edit_rule">
              <input type="hidden" name="dialogue_rule_id" value="<?= $this->id ?>">
              <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
              <label>
                Rule-Text:
                <textarea name="rule_text" style="width: 100%; height: 100px"><?= $this->rule_text ?></textarea>
              </label>
              <br>
              <button class="button" type="submit">Edit</button>
            </form>
            <br>
            <form method="post">
              <?= ($delete_dialogue_rule_error ?? null)?->get_error_card() ?>
              <input type="hidden" name="action" value="delete_rule">
              <input type="hidden" name="dialogue_rule_id" value="<?= $this->id ?>">
              <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
              <button class="button" type="submit">Delete</button>
            </form>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
  }
}