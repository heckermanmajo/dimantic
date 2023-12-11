<?php
declare(strict_types=1);

namespace cls\data\account;

use cls\App;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMembership;
use cls\DataClass;

class NewsEntry extends DataClass {

  /**
   * If a member gets invited to a new dialogue.
   */
  const TYPE_INVITED_TO_NEW_DIALOGUE = 'invited_to_new_dialoge';
  /**
   * If a new message is written in a dialogue that i am member of or i am creator of.
   */
  const TYPE_NEW_MESSAGE_IN_DIALOGUE = 'new_message_in_dialoge';
  /**
   * If other person has accepted invitation to my dialogue.
   */
  const TYPE_OTHER_PERSON_HAS_ACCEPTED_INVITATION = 'other_person_has_accepted_invitation';
  /**
   * If other person has declined invitation to my dialogue.
   */
  const TYPE_OTHER_PERSON_HAS_DECLINED_INVITATION = 'other_person_has_declined_invitation';
  /**
   * If te owner of a dialogue i joined, has started the dialogue.
   */
  const TYPE_DIALOGUE_HAS_STARTED = 'dialoge_has_started';


  # todo: left dialogue
  # todo: answer time has expired ...
  # todo: account X has send you a message
  # todo: somebody you follow, has finished a dialogue
  # todo: need to answer in max f.e. 3 days ...
  ###########################################################################
  #                                                                         #
  #  Properties & Property-functions                                        #
  #                                                                         #
  ###########################################################################
  /** The id of the account this news entry is directed to. */
  var int $account_id = 0;
  /**
   * If the news entry has been read by the account.
   * 0 = not read
   * 1 = read
   */
  var int $read = 0;
  /**
   * The id of the dialogue this news entry has as context.
   * Can be 0 if the news entry is not related to a dialogue.
   */
  var int $dialogue_id = 0;
  /**
   * Type of the news entry.
   * @see NewsEntry::TYPE_* constants
   */
  var string $type = "";

  #################################
  ###### Joined Values      #######
  #################################

  #################################
  ###### Property-functions #######
  #################################

  ###########################################################################
  #                                                                         #
  #  Model-Queries                                                          #
  #                                                                         #
  ###########################################################################

  static function get_my_news(App $app): array {
    return NewsEntry::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM `NewsEntry` WHERE `account_id` = ?",
      params: [$app->get_currently_logged_in_account()->id]
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
  function get_news_card(App $app): string {
    ob_start();
    ?>
    <div class="w3-card w3-margin w3-padding">
      <h6>NewsEntry</h6>
      <pre><?=json_encode($this, JSON_PRETTY_PRINT)?></pre>
    </div>
    <?php
    return ob_get_clean();
  }
}