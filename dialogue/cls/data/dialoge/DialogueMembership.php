<?php
declare(strict_types=1);

namespace cls\data\dialoge;

use cls\App;
use cls\data\account\Account;
use cls\DataClass;
use cls\RequestError;
use PDO;


class DialogueMembership extends DataClass {
  const STATE_PENDING = 'pending';
  const STATE_ACTIVE = 'active';
  const STATE_DECLINED = 'declined';

  const TYPE_INVITATION = 'invitation';
  const TYPE_JOIN_REQUEST = 'join_request';
  const TYPE_CREATOR = 'creator';
  ###########################################################################
  #                                                                         #
  #  Properties & Property-functions                                        #
  #                                                                         #
  ###########################################################################
  var int $last_message_seen_id = 0;
  var int $dialogue_id = 0;
  var int $account_id = 0;
  var string $state = '';
  var string $type = '';
  var string $create_date = '';

  /**
   * @var string All members of a dialogue have a notes field
   * where they can takle private notes ...
   */
  var string $notes_field = '';

  /**
   * @var string This is the draft of the next message
   * that will be sent to the dialogue, if the user
   * publishes it AND the time util publish is reached.
   */
  var string $next_message_draft = '';

  /**
   * @var string This is the draft-text of a rule
   * authored by the member of this entry - not published
   * yet.
   */
  var string $rule_draft = '';

  #################################
  ###### Joined Values      #######
  #################################

  #################################
  ###### Property-functions #######
  #################################
  function get_associated_dialoge(PDO $connection): Dialogue {
    return Dialogue::get_by_id(pdo: $connection, id: $this->dialogue_id);
  }

  function get_associated_account(PDO $connection): Account {
    return Account::get_by_id(pdo: $connection, id: $this->account_id);
  }

  static function value_is_correct_type(string $type): bool {
    return in_array(
      needle: $type,
      haystack: [self::TYPE_INVITATION, self::TYPE_JOIN_REQUEST]
    );
  }

  static function value_is_correct_state(string $state): bool {
    return in_array(
      needle: $state,
      haystack: [self::STATE_ACTIVE, self::STATE_DECLINED, self::STATE_PENDING]
    );
  }

  ###########################################################################
  #                                                                         #
  #  Model-Queries                                                          #
  #                                                                         #
  ###########################################################################

  static function get_my_membership_by_dialogue(
    int $dialogue_id,
    App $app
  ): ?static {
    return static::get_one(
      $app->get_database(),
      "SELECT * FROM `DialogueMembership` WHERE `dialogue_id` = ? AND `account_id` = ?",
      [$dialogue_id, $app->get_currently_logged_in_account()->id],
    );
  }

  /**
   * @return array<int, static>
   */
  static function get_all_memberships_by_user(
    int $account_id, string $status, string $type, PDO $account
  ): array {

  }

  /**
   * @return array<int, static>
   */
  static function get_all_memberships_for_dialoge(
    int $account_id, PDO $account
  ): array {

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

  function get_info_bar(App $app) : string {
    ob_start();
    ?>
    <div class="w3-card w3-margin w3-padding">
      <?php if ($this->state == static::STATE_PENDING): ?>
        <div class="w3-panel w3-yellow">
          <p>pending</p>
        </div>
      <?php elseif ($this->state == static::STATE_ACTIVE): ?>
        <div class="w3-panel w3-green">
          <p>active</p>
        </div>
      <?php elseif ($this->state == static::STATE_DECLINED): ?>
        <div class="w3-panel w3-red">
          <p>declined</p>
        </div>
      <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
  }
}