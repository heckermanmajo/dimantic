<?php
declare(strict_types=1);

namespace cls\data\dialoge;

use cls\App;
use cls\data\account\Account;
use cls\DataClass;

use Exception;

/**
 * This class represents one dialogue between multiple
 * members.
 *
 */
class Dialogue extends DataClass {

  /**
   * If a dialogue has not yet started, members can join and its content
   * can be changed.
   * @deprecated
   */
  const STATE_NOT_YET_STARTED = 'not_yet_started';
  /**
   * If a dialogue is open, members can write messages.
   */
  const STATE_OPEN = 'open';
  /**
   * If a dialogue is closed, members can not write messages anymore.
   */
  const STATE_CLOSED = 'closed';

  ###########################################################################
  #                                                                         #
  #  Properties & Property-functions                                        #
  #                                                                         #
  ###########################################################################

  /**
   * @var int The id of the blue-print, this dialogue is created from.
   */
  var int $blue_print_id = 0;

  /**
   * The state of the dialogue.
   * @see Dialogue::STATE_* constants
   */
  var string $state = '';

  /**
   * The id of the account that has created the dialogue.
   */
  # todo: set to to 0
  var int $author_id = 0;

  var int $created_at = 0;

  #################################
  ###### Joined Values      #######
  #################################

  #################################
  ###### Property-functions #######
  #################################

  /**
   * Return the messages in descending id order.
   * @param App $app
   * @return array<DialogueMessage>
   */
  function get_all_messages(App $app, bool $cache = false): array {
    # static $cache = [];
    # todo: cache the result -> with optional param to not cache
    return DialogueMessage::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `DialogueMessage` WHERE `dialogue_id` = ? ORDER BY id DESC",
      params: [$this->id]
    );
  }

  /**
   * @param App $app
   * @return array<DialogueMembership>
   */
  function get_memberships(App $app): array {
    return DialogueMembership::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `DialogueMembership` WHERE `dialogue_id` = ?",
      params: [$this->id]
    );
  }

  function get_membership_of_given_account(App $app, int $account_id): ?DialogueMembership {
    return DialogueMembership::get_one(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `DialogueMembership` WHERE `dialogue_id` = ? AND `account_id` = ?",
      params: [$this->id, $account_id]
    );
  }

  function get_number_of_memberships(
    App $app
  ): int {
    return DialogueMembership::get_count(
      pdo: $app->get_database(),
      sql: "SELECT COUNT(*) FROM `DialogueMembership` WHERE `dialogue_id` = ?",
      params: [$this->id]
    );
  }

  /**
   * This function returns the number of all characters in the dialogue,
   * that are not html tags (after markdown has been parsed).
   *
   * This is used to calculate the number of used and to be used like credits.
   *
   * @param App $app
   * @return int
   *
   * @see DialogueMembership::$like_percentage
   * @see DialogueMessage::get_view_card()
   *
   */
  function get_number_of_all_chars_in_messages_text(App $app): int {
    $all_messages = $this->get_all_messages($app);
    $sum = 0;
    foreach ($all_messages as $message) {
      $html_content = $app->markdown_to_html($message->content);
      $pure_text = strip_tags($html_content);
      $sum += strlen($pure_text);
    }
    return $sum;
  }

  /**
   * Returns all members that have left the dialogue.
   * -> If all but one have left, the dialogue is closed.
   * @param App $app
   * @return int
   */
  function get_number_of_members_left(App $app): int {
    $all_memberships = $this->get_memberships($app);
    $members_that_have_left = 0;
    foreach ($all_memberships as $membership) {
      if ($membership->state == DialogueMembership::STATE_LEFT) {
        $members_that_have_left++;
      }
    }
    return $members_that_have_left;
  }

  function get_last_message(App $app): ?DialogueMessage {
    return DialogueMessage::get_one(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `DialogueMessage` WHERE `dialogue_id` = ? ORDER BY `create_date` DESC LIMIT 1",
      params: [$this->id]
    );
  }

  function current_user_is_member(App $app): bool {
    return $this->get_membership_of_given_account($app, $app->get_currently_logged_in_account()->id) != null;
  }

  /**
   * @param App $app
   * @return array<DialogueRule>
   */
  function get_rules_of_dialogue(App $app): array {
    $rules = DialogueRule::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `DialogueRule` WHERE `dialogue_id` = ?",
      params: [$this->id]
    );
    foreach ($rules as $count => $rule) {
      $rule->__rule_order = $count;
    }
    return $rules;
  }

###########################################################################
#                                                                         #
#  Model-Queries                                                          #
#                                                                         #
###########################################################################

  /**
   * @param int $offset
   * @param int $limit
   * @param App $app
   * @return array<Dialogue>
   */
  static function get_my_dialoges(int $offset, int $limit, App $app): array {
    return static::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `Dialogue` WHERE `id` 
               IN (SELECT `dialogue_id` FROM `DialogueMembership` WHERE `account_id` = ?)",
      params: [$app->get_currently_logged_in_account()->id]
    );
  }

###########################################################################
#                                                                         #
#  Logic & Controller                                                     #
#                                                                         #
###########################################################################

  /**
   * We want alternating turns of messages.
   *
   * @param App $app
   * @return bool
   * @throws Exception
   */
  function next_turn_is_my_turn(App $app): bool {
    $messages = $this->get_all_messages($app);
    if (count($messages) == 0) {
      # if no messages are there, the first message should be written
      # not by the creator, but by the invited member
      # since the creator has written the initial dialogue content
      if ($this->author_id == $app->get_currently_logged_in_account()->id) {
        return false;
      }
      return true;
    }
    $last_message = $messages[0];
    if ($last_message->account_id == $app->get_currently_logged_in_account()->id) {
      return false;
    }
    return true;
  }

###########################################################################
#                                                                         #
#  Views                                                                  #
#                                                                         #
###########################################################################
  function get_header_bar(App $app): string {
    $my_membership = $this->get_membership_of_given_account(
      app: $app,
      account_id: $app->get_currently_logged_in_account()->id
    );

    $author = Account::get_by_id(
      pdo: $app->get_database(),
      id: $this->author_id
    );

    $i_am_part_of_dialogue = $my_membership != null;

    $my_membership_hint = "";
    if ($i_am_part_of_dialogue) {
      $my_membership_hint = "<small style='color: greenyellow'>Member</small>";
    }

    $state_hint = match ($this->state) {
      Dialogue::STATE_OPEN => "<small style='color: greenyellow'>Open</small>",
      Dialogue::STATE_CLOSED => "<small style='color: #b4aa50'>Closed</small>",
      default => "DEPRECATED",
    };

    $number_of_members = $this->get_number_of_memberships($app);
    ob_start();

    ?>
    <div>

      <?php if ($i_am_part_of_dialogue && $this->next_turn_is_my_turn($app)): ?>
        <b style="color: #00ff78">MY TURN</b> |
      <?php else: ?>
        <b style="color: #797979">THEIR TURN</b> |
      <?php endif; ?>

      <?= $my_membership_hint ?> | <?= $state_hint ?> | <small>members: <?= $number_of_members ?></small>
      | <small>created: <?= $this->created_at ?></small>
      | <small>author: <b><?= $author->name ?></b></small>
    </div>
    <?php
    return ob_get_clean();
  }

  function get_overview_card(
    App $app,
  ): string {

    ob_start();

    $content = "Empty, but load later from blueprint.";
    ?>

    <div class="sketch-card w3-margin w3-padding">
      <small><?= $this->get_header_bar($app) ?></small>
      <a style="text-decoration: none" href="/dialogue.php?id=<?= $this->id ?>">
        <?= $content ?>
      </a>
    </div>
    <?php
    return ob_get_clean();
  }

  static function check_value(string $field_name, mixed $value, App $app): string|null {
    return null;
  }

}