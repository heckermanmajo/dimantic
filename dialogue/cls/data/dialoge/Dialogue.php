<?php
declare(strict_types=1);

namespace cls\data\dialoge;

use cls\App;
use cls\data\account\Account;
use cls\DataClass;
use cls\RequestError;

/**
 * This class represents one dialogue between multiple
 * members.
 *
 */
class Dialogue extends DataClass {

  /**
   * If a dialogue has not yet started, members can join and its content
   * can be changed.
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
   * The content of the dialogue.
   * This can be used to describe the topic of the dialogue.
   * Also: add rules or other information.
   *
   * It is parsed by the interpreter.
   */
  var string $content = '';

  /**
   * The state of the dialogue.
   * @see Dialogue::STATE_* constants
   */
  var string $state = '';

  /**
   * The number of days you have to answer to a message.
   * -> If the member fails to answer, we offer the other member
   * to end the dialogue.
   */
  var int $number_of_days_to_reply = 0;

  /**
   * The number of hours you have to wait until you can send a new message.
   */
  var int $message_cooldown_in_hours = 0;

  /**
   * The number of words you can write per message.
   */
  var int $max_words_per_message = 0;

  /**
   * The number of members that are needed to start the dialogue.
   */
  var int $number_of_needed_members = 1;

  /**
   * The id of the account that has created the dialogue.
   */
  # todo: set to to 0
  var int $author_id = 3;

  /**
   * The date the dialogue has been created.
   * Format: YYYY-MM-DD HH:MM:SS
   */
  var string $create_date = '';

  /**
   * If the invited member declines the invitation, the dialogue is dead.
   */
  var int $dead = 0;

  #################################
  ###### Joined Values      #######
  #################################

  #################################
  ###### Property-functions #######
  #################################

  function get_title(App $app): string {
    # todo: add later the correct title via interpreter
    return $this->content;
  }

  /**
   * Return the messages in descending id order.
   * @param App $app
   * @return array<DialogueMessage>
   */
  function get_all_messages(App $app): array {
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
    App  $app,
    bool $ignore_pending = false,
    bool $ignore_declined = true,
  ): int {
    if ($ignore_pending && $ignore_declined) {
      return DialogueMembership::get_count(
        pdo: $app->get_database(),
        sql: "SELECT COUNT(*) FROM `DialogueMembership` WHERE `dialogue_id` = ? AND `state` = ?",
        params: [$this->id, DialogueMembership::STATE_ACTIVE]
      );
    }
    if ($ignore_pending) {
      return DialogueMembership::get_count(
        pdo: $app->get_database(),
        sql: "SELECT COUNT(*) FROM `DialogueMembership` WHERE `dialogue_id` = ? AND (`state` = ? OR `state` = ?)",
        params: [$this->id, DialogueMembership::STATE_ACTIVE, DialogueMembership::STATE_DECLINED]
      );
    }
    if ($ignore_declined) {
      return DialogueMembership::get_count(
        pdo: $app->get_database(),
        sql: "SELECT COUNT(*) FROM `DialogueMembership` WHERE `dialogue_id` = ? AND (`state` = ? OR `state` = ?)",
        params: [$this->id, DialogueMembership::STATE_ACTIVE, DialogueMembership::STATE_PENDING]
      );
    }
    return DialogueMembership::get_count(
      pdo: $app->get_database(),
      sql: "SELECT COUNT(*) FROM `DialogueMembership` WHERE `dialogue_id` = ?",
      params: [$this->id]
    );
  }

  function get_only_active_memberships(App $app): array {
    return DialogueMembership::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `DialogueMembership` WHERE `dialogue_id` = ? AND `state` = ?",
      params: [$this->id, DialogueMembership::STATE_ACTIVE]
    );
  }

  function get_member_accounts_(App $app): array {
    return Account::get_array(
      $app->get_database(),
      "SELECT * FROM Account WHERE id IN 
        (SELECT account_id FROM DialogueMembership WHERE dialogue_id = :dialogue_id)",
      [
        "dialogue_id" => $this->id
      ]
    );
  }

  function is_closed(): bool { }

  function get_last_message(App $app): DialogueMessage {
    return DialogueMessage::get_one(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `DialogueMessage` WHERE `dialogue_id` = ? ORDER BY `create_date` DESC LIMIT 1",
      params: [$this->id]
    );
  }

  function get_last_message_date(): string { }

  function has_not_yet_started(): bool { }

###########################################################################
#                                                                         #
#  Model-Queries                                                          #
#                                                                         #
###########################################################################
  static function find_dialoges_by_search_string(
    string $search_string,
    int    $offset,
    int    $limit
  ): array {
  }

  static function get_my_ongoing_dialogues(int $offset, int $limit, App $app): array {
    return Dialogue::get_array(
      $app->get_database(),
      "SELECT * FROM Dialogue WHERE id IN 
        (SELECT dialogue_id FROM DialogueMembership 
            WHERE account_id = :account_id AND DialogueMembership.state = :membership_state)
        AND Dialogue.state = :dialogue_state",
      [
        "account_id" => $app->get_currently_logged_in_account()->id,
        "membership_state" => DialogueMembership::STATE_ACTIVE,
        "dialogue_state" => Dialogue::STATE_OPEN
      ]
    );
  }

  static function my_dialogues_ready_to_start(int $offset, int $limit, App $app): array {
    return Dialogue::get_array(
      $app->get_database(),
      "SELECT * FROM Dialogue WHERE id IN 
        (SELECT dialogue_id FROM DialogueMembership 
            WHERE account_id = :account_id AND DialogueMembership.state = :membership_state)
        AND Dialogue.state = :dialogue_state",
      [
        "account_id" => $app->get_currently_logged_in_account()->id,
        "membership_state" => DialogueMembership::STATE_ACTIVE,
        "dialogue_state" => Dialogue::STATE_NOT_YET_STARTED
      ]
    );
  }

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

  static function get_dialoges_i_have_unseen_messages_in(int $offset, int $limit): array {

  }

  static function get_count_of_dialoges_i_have_unseen_messages_in(): int {

  }

  static function get_dialogues_by_state(int $offset, int $limit, string $state, App $app): array {
    return static::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `Dialogue` WHERE `state` = ?",
      params: [$state]
    );
  }

  /**
   * Returns the dialogues i am invited to, but have not yet joined.
   * @param int $offset
   * @param int $limit
   * @return array<Dialogue>
   */
  static function get_dialogues_i_am_invited_to(int $offset, int $limit, App $app): array {
    return Dialogue::get_array(
      $app->get_database(),
      "SELECT * FROM Dialogue WHERE id IN 
        (SELECT dialogue_id FROM DialogueMembership WHERE account_id = :account_id AND state = :state)",
      [
        "account_id" => $app->get_currently_logged_in_account()->id,
        "state" => DialogueMembership::STATE_PENDING
      ]
    );
  }

###########################################################################
#                                                                         #
#  Logic & Controller                                                     #
#                                                                         #
###########################################################################

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

    $accept_invitation_button = false;
    $i_am_part_of_dialogue = false;
    if ($my_membership == null) {
      $my_membership_hint = "<small>You are not a <b style='color: #ffd205'>member</b> of this dialogue</small>";
    }
    else {
      if ($my_membership->type == DialogueMembership::TYPE_CREATOR) {
        $my_membership_hint = "<small>You are the <b style='color: #69ff7a'>creator</b> of this dialogue</small>";
        $i_am_part_of_dialogue = true;
      }
      else {
        if ($my_membership->state == DialogueMembership::STATE_PENDING) {
          $my_membership_hint = "<small><b style='color: #00bcd4'>You have been requested to join this dialogue</b></small>";
          $accept_invitation_button = true;
        }
        else {
          if ($my_membership->state == DialogueMembership::STATE_DECLINED) {
            $my_membership_hint = "<small><b style='color: #e400ff'>You have declined to join this dialogue</b></small>";
          }
          else {
            $my_membership_hint = "<small>You are a <b style='color: yellow'>member</b> of this dialogue</small>";
            $i_am_part_of_dialogue = true;
          }
        }
      }
    }

    $state_hint = match ($this->state) {
      Dialogue::STATE_NOT_YET_STARTED => "<small style='color: white'>Not yet started</small>",
      Dialogue::STATE_OPEN => "<small style='color: greenyellow'>Open</small>",
      Dialogue::STATE_CLOSED => "<small style='color: #b4aa50'>Closed</small>",
    };

    $number_of_members = $this->get_number_of_memberships($app);

    $number_of_active_memberships = count($this->get_only_active_memberships($app));

    ob_start();
    ?>
    <div>
      <?php if ($this->dead == 1): ?>
        <b style="color: red">DEAD</b> |
      <?php endif; ?>
      <?php if ($this->state == Dialogue::STATE_OPEN && $i_am_part_of_dialogue && !$this->dead == 1): ?>
        <?php if ($this->next_turn_is_my_turn($app)): ?>
          <b style="color: #00ff78">MY TURN</b> |
        <?php else: ?>
          <b style="color: #797979">THEIR TURN</b> |
        <?php endif; ?>
      <?php endif; ?>
      <?= $my_membership_hint ?> | <?= $state_hint ?> | <small>members: <?= $number_of_members ?></small>
      | <small>active members: <?= $number_of_active_memberships ?></small>
      | <small>created: <?= $this->create_date ?></small>
      | <small>author: <b><?= $author->name ?></b></small>
      <?php if ($accept_invitation_button): ?>
        <form method="post" style="display: inline-block">
          <input type="hidden" name="action" value="accept_dialogue_invitation">
          <input type="hidden" name="dialogue_id" value="<?= $this->id ?>">
          <button class="button">Accept Invitation</button>
        </form>
        <form method="post" style="display: inline-block">
          <input type="hidden" name="action" value="decline_invitation">
          <input type="hidden" name="dialogue_id" value="<?= $this->id ?>">
          <button class="button">Decline Invitation</button>
        </form>
      <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
  }

  function get_overview_card(
    App           $app,
    ?RequestError $activate_error = null
  ): string {

    $can_be_started = count($this->get_only_active_memberships($app))
      >= $this->number_of_needed_members;

    $is_started = $this->state != Dialogue::STATE_NOT_YET_STARTED;

    ob_start();
    ?>

    <div class="w3-card w3-margin w3-padding">
      <p><?= $this->get_header_bar($app) ?></p>
      <a style="text-decoration: none" href="/dialogue.php?id=<?= $this->id ?>">
        <?php if (trim($this->content) == ""): ?>
          <h4>Untitled Dialogue</h4>
        <?php else: ?>
          <h4><?= $this->content ?></h4>
        <?php endif; ?>
        <!--<div>
          <small>created: <?= $this->create_date ?></small>
        </div>-->
      </a>
      <?php if (
        $can_be_started
        && !$is_started
        && $this->author_id == $app->get_currently_logged_in_account()->id
      ) { ?>
        <form method="post" style="display: inline-block">
          <?= $activate_error?->get_error_card() ?>
          <input type="hidden" name="action" value="activate_dialogue">
          <input type="hidden" name="dialogue_id" value="<?= $this->id ?>">
          <button class="button">Start Dialogue</button>
        </form>
      <?php } ?>
      <!--<pre><?= json_encode($this, JSON_PRETTY_PRINT) ?></pre>-->
    </div>
    <?php
    return ob_get_clean();
  }

  static function check_value(string $field_name, mixed $value, App $app): string|null {
    return null;
  }

  function get_dialoge_card() { }
}