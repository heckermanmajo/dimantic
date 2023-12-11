<?php
declare(strict_types=1);

namespace cls\data\dialoge;

use cls\App;
use cls\data\account\Account;
use cls\DataClass;
use cls\RequestError;
use PDO;


class DialogueMembership extends DataClass {

  const STATE_LEFT = 'left';
  const STATE_ACTIVE = 'active';
  const STATE_MODERATOR = 'moderator';

  ###########################################################################
  #                                                                         #
  #  Properties & Property-functions                                        #
  #                                                                         #
  ###########################################################################
  var int $last_message_seen_id = 0;
  var int $dialogue_id = 0;
  var int $account_id = 0;
  /**
   * todo: state needs to become "left" or "active"
   * @var string
   */
  var string $state = 'active';

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

  /**
   * Percentage of chars that can be maximally liked
   * by users.
   */
  var float $like_percentage = 0.1;

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

  ###########################################################################
  #                                                                         #
  #  Logic & Controller                                                     #
  #                                                                         #
  ###########################################################################

  function get_absolute_amount_of_all_possible_like_credits(App $app): int {
    $dialogue = $this->get_associated_dialoge($app->get_database());
    $all_text_chars_in_dialogue = $dialogue->get_number_of_all_chars_in_messages_text($app);
    return (int)(((float)$all_text_chars_in_dialogue) * $this->like_percentage);
  }

  /**
   * @throws \Exception
   */
  function get_absolute_amount_of_FREE_like_credits(App $app): int {
    $all_possible_like_credits = $this->get_absolute_amount_of_all_possible_like_credits($app);
    $all_used_like_credits = DialogueMessageSelectionLike::get_all_used_like_credits_per_person_per_dialogue(
      $app,
      $this->dialogue_id,
      $this->account_id
    );
    return $all_possible_like_credits - $all_used_like_credits;
  }

  ###########################################################################
  #                                                                         #
  #  Views                                                                  #
  #                                                                         #
  ###########################################################################

}