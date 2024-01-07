<?php
declare(strict_types=1);

namespace cls\data\dialoge;

use cls\App;
use cls\data\account\Account;
use cls\data\conversation_blue_print\ConversationBluePrint;
use cls\data\conversation_blue_print\Lobby;
use cls\data\conversation_blue_print\LobbyMembership;
use cls\data\conversation_blue_print\ProtoRule;
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
  var int $author_id = 0;
  
  var int $created_at = 0;
  
  /**
   * The description of the dialogue.
   * Just a copy of the description of the blueprint.
   *
   */
  var string $description = '';
  
  #################################
  ###### Joined Values      #######
  #################################
  
  #################################
  ###### Property-functions #######
  #################################
  
  /**
   * Return the messages in descending id order.
   * @param bool $cache
   * @return array<DialogueMessage>
   * @throws Exception
   */
  function get_all_messages(bool $cache = false): array {
    # static $cache = [];
    # todo: cache the result -> with optional param to not cache
    return DialogueMessage::get_array(
      pdo:    App::get()->get_database(),
      sql:    "SELECT * FROM `DialogueMessage` WHERE `dialogue_id` = ? ORDER BY id DESC",
      params: [$this->id]
    );
  }
  
  /**
   * Retrieves the memberships associated with the dialogue.
   *
   * @return array<DialogueMembership> The DialogueMembership objects representing the memberships of the dialogue.
   * @throws Exception If an error occurs while retrieving the memberships.
   *
   */
  function get_memberships(): array {
    return DialogueMembership::get_array(
      pdo:    App::get()->get_database(),
      sql:    "SELECT * FROM `DialogueMembership` WHERE `dialogue_id` = ?",
      params: [$this->id]
    );
  }
  
  /**
   * Retrieves the membership of a given account in the dialogue.
   *
   * @param int $account_id The ID of the account to retrieve the membership for.
   *
   * @return DialogueMembership|null The DialogueMembership object representing the membership of the account in the dialogue, or null if the account is not a member.
   * @throws Exception If an error occurs during the database query.
   *
   */
  function get_membership_of_given_account(int $account_id): ?DialogueMembership {
    return DialogueMembership::get_one(
      pdo:    App::get()->get_database(),
      sql:    "SELECT * FROM `DialogueMembership` WHERE `dialogue_id` = ? AND `account_id` = ?",
      params: [$this->id, $account_id]
    );
  }
  
  /**
   * This function returns the number of memberships for the current dialogue.
   *
   * @return int The number of memberships.
   *
   * @throws Exception
   * @see DialogueMembership
   */
  function get_number_of_memberships(): int {
    return DialogueMembership::get_count(
      pdo:    App::get()->get_database(),
      sql:    "SELECT COUNT(*) FROM `DialogueMembership` WHERE `dialogue_id` = ?",
      params: [$this->id]
    );
  }
  
  /**
   * This function returns the number of all characters in the dialogue,
   * that are not html tags (after markdown has been parsed).
   *
   * This is used to calculate the number of used and to be used like credits.
   *
   * @return int
   *
   * @see DialogueMembership::$like_percentage
   * @see DialogueMessage::get_view_card()
   *
   */
  function get_number_of_all_chars_in_messages_text(): int {
    $all_messages = $this->get_all_messages();
    $sum = 0;
    foreach ($all_messages as $message) {
      $html_content = App::get()->markdown_to_html($message->content);
      $pure_text = strip_tags($html_content);
      $sum += strlen($pure_text);
    }
    return $sum;
  }
  
  /**
   * Returns all members that have left the dialogue.
   * -> If all but one have left, the dialogue is closed.
   * @return int
   * @throws Exception
   */
  function get_number_of_members_left(): int {
    $all_memberships = $this->get_memberships();
    $members_that_have_left = 0;
    foreach ($all_memberships as $membership) {
      if ($membership->state == DialogueMembership::STATE_LEFT) {
        $members_that_have_left++;
      }
    }
    return $members_that_have_left;
  }
  
  /**
   * Get the last message of the dialogue.
   *
   * @return ?DialogueMessage Returns the last message of the dialogue, or null if no message exists.
   * @throws Exception
   */
  function get_last_message(): ?DialogueMessage {
    return DialogueMessage::get_one(
      pdo:    App::get()->get_database(),
      sql:    "SELECT * FROM `DialogueMessage` WHERE `dialogue_id` = ? ORDER BY `create_date` DESC LIMIT 1",
      params: [$this->id]
    );
  }
  
  /**
   * @throws Exception
   */
  function current_user_is_member(): bool {
    return $this->get_membership_of_given_account(
        account_id: App::get()->get_currently_logged_in_account()->id
      ) != null;
  }
  
  /**
   * @return array<DialogueRule>
   * @throws Exception
   */
  function get_rules_of_dialogue(): array {
    $rules = DialogueRule::get_array(
      pdo:    App::get()->get_database(),
      sql:    "SELECT * FROM `DialogueRule` WHERE `dialogue_id` = ?",
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
   * @return array<Dialogue>
   * @throws Exception
   */
  static function get_my_dialoges(int $offset, int $limit): array {
    return static::get_array(
      pdo:    App::get()->get_database(),
      sql:    "SELECT * FROM `Dialogue` WHERE `id`
               IN (SELECT `dialogue_id` FROM `DialogueMembership` WHERE `account_id` = ?)",
      params: [App::get()->get_currently_logged_in_account()->id]
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
   * @return bool
   * @throws Exception
   */
  function next_turn_is_my_turn(): bool {
    $messages = $this->get_all_messages();
    if (count($messages) == 0) {
      # if no messages are there, the first message should be written
      # not by the creator, but by the invited member
      # since the creator has written the initial dialogue content
      if ($this->author_id == App::get()->get_currently_logged_in_account()->id) {
        return false;
      }
      return true;
    }
    $last_message = $messages[0];
    if ($last_message->account_id == App::get()->get_currently_logged_in_account()->id) {
      return false;
    }
    return true;
  }
  
  ###########################################################################
  #                                                                         #
  #  Views                                                                  #
  #                                                                         #
  ###########################################################################
  /**
   * This function returns the header bar of the dialogue card as string.
   *
   * @throws Exception
   */
  function get_header_bar(): string {
    
    [$log, $warn, $err, $todo]
      = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    
    $app = App::get();
    
    $my_membership = $this->get_membership_of_given_account(
      account_id: $app->get_currently_logged_in_account()->id
    );
    
    $author = Account::get_by_id(
      pdo: $app->get_database(),
      id:  $this->author_id
    );
    
    $i_am_part_of_dialogue = $my_membership != null;
    
    $my_membership_hint = "";
    if ($i_am_part_of_dialogue) {
      $my_membership_hint = "<small style='color: greenyellow'>Member</small>";
    }
    
    $state_hint = match ($this->state) {
      Dialogue::STATE_OPEN => "<small style='color: greenyellow'>Open</small>",
      Dialogue::STATE_CLOSED => "<small style='color: #b4aa50'>Closed</small>",
      default => "DEPRECATED-State",
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
      
      <?= $my_membership_hint ?> | <?= $state_hint ?> |
      <small>members: <?= $number_of_members ?></small>
      | <small>created: <?= $this->created_at ?></small>
      | <small>author: <b><?= $author->name ?></b></small>
    </div>
    <?php
    return ob_get_clean();
  }
  
  /**
   * @throws Exception
   */
  function get_overview_card(): string {
    [$log, $warn, $err, $todo]
      = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    ob_start();
    
    ?>

    <div class="sketch-card w3-margin w3-padding">
      <small><?= $this->get_header_bar() ?></small>
      <a style="text-decoration: none" href="/dialogue.php?id=<?= $this->id ?>">
        <?= App::get()->markdown_to_html($this->description) ?>
      </a>
    </div>
    <?php
    return ob_get_clean();
  }
  
  static function check_value(string $field_name, mixed $value): string|null {
    return null;
  }
  
  /**
   * This function creates a dialogue from a given blueprint and lobby.
   * It also creates the memberships and rules.
   *
   * @param ConversationBluePrint $blueprint The blueprint to create the dialogue from.
   * @param Lobby $lobby The lobby to create the dialogue from.
   * @param bool $save_directly_to_db If true, the dialogue, memberships and rules are saved directly to the database.
   * @param bool $create_news_for_users If true, news-entries are created for the users.
   *
   * @return array{
   *   dialogue: Dialogue,
   *   memberships: array<DialogueMembership>,
   *   rules: array<DialogueRule>,
   * }
   *
   * @throws Exception
   * @see DialogueMembership
   *
   * @see DialogueRule
   */
  static function create_dialogue_from_given_blueprint_and_lobby(
    ConversationBluePrint $blueprint,
    Lobby                 $lobby,
    bool                  $save_directly_to_db = false,
    bool                  $create_news_for_users = false,
  ): array {
    
    [$log, $warn, $err, $todo]
      = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    
    $app = App::get();
    
    $dialogue = new Dialogue();
    $dialogue->blue_print_id = $lobby->conversation_blueprint_id;
    $dialogue->description = $blueprint->description;
    $dialogue->author_id = $blueprint->author_id;
    
    if ($save_directly_to_db) {
      $dialogue->save($app->get_database());
    }
    
    $lobby_memberships = LobbyMembership::get_memberships_of_lobby(
      $lobby->id
    );
    
    $memberships = [];
    
    foreach ($lobby_memberships as $lobby_membership) {
      
      $user_to_create_membership_for = $lobby_membership->account_id;
      $dialogue_membership = new DialogueMembership();
      $dialogue_membership->dialogue_id = $dialogue->id;
      $dialogue_membership->account_id = $user_to_create_membership_for;
      $dialogue_membership->state = DialogueMembership::STATE_ACTIVE;
      
      if ($save_directly_to_db) {
        $dialogue_membership->save($app->get_database());
      }
      
      $memberships[] = $dialogue_membership;
      
      if ($create_news_for_users) {
        # todo: create news for the users
        $warn("IMPLEMENT ME: create news for the users");
      }
      
    }
    
    
    $proto_rules = ProtoRule::get_array(
      $app->get_database(),
      "SELECT * FROM ProtoRule WHERE blue_print_id = ?",
      [$blueprint->id]
    );
    
    $rules = [];
    
    foreach ($proto_rules as $proto_rule) {
      
      $dialogue_rule = new DialogueRule();
      $dialogue_rule->dialogue_id = $dialogue->id;
      $dialogue_rule->rule_text = $proto_rule->content;
      # the creator of the blueprint is the author of the rule
      $dialogue_rule->account_id = $blueprint->author_id;
      $dialogue_rule->was_proto_rule = 1;
      
      if ($save_directly_to_db) {
        $dialogue_rule->save($app->get_database());
      }
      
      $rules[] = $dialogue_rule;
    }
    
    return [
      'dialogue'    => $dialogue,
      'memberships' => $memberships,
      'rules'       => $rules,
    ];
    
  } // end function create_dialogue_from_given_blueprint_and_lobby(...)
  
}