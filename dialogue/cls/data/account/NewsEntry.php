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
      <?php switch ($this->type):

        case static::TYPE_INVITED_TO_NEW_DIALOGUE:
          $new_dialogue = Dialogue::get_by_id(
            pdo: $app->get_database(),
            id: $this->dialogue_id
          );
          ?>
          <h4>You have been invited to a new dialogue!</h4>
          <p><?= $new_dialogue->get_title($app) ?></p>
          <a class="button" href="/dialogue.php?id=<?= $new_dialogue->id ?>">Go to dialogue </a>
          <?php
          break;

        case static::TYPE_NEW_MESSAGE_IN_DIALOGUE:
          $dialogue = Dialogue::get_by_id(
            pdo: $app->get_database(),
            id: $this->dialogue_id
          );
          ?>
          <h4>New message in dialogue!</h4>
          <p><?= $dialogue->get_title($app) ?></p>
          <pre><?= $dialogue->get_last_message($app)->get_preview_of_content() ?></pre>
          <?php
          break;

        case static::TYPE_OTHER_PERSON_HAS_ACCEPTED_INVITATION:
          $dialogue = Dialogue::get_by_id(
            pdo: $app->get_database(),
            id: $this->dialogue_id
          );
          # todo: later more than two people can be members, fix this
          $memberships = $dialogue->get_memberships($app);
          $other_person = null;
          foreach ($memberships as $m){
            if($m->type == DialogueMembership::TYPE_CREATOR){
              continue;
            }
            $other_person = $m->get_associated_account($app->get_database());
          }
          ?>
          <h4><b><?=$other_person->name?></b> has joined dialogue!</h4>
          <p><?= $dialogue->get_title($app) ?></p>
          <p></p>
          <?php
          break;

        case static::TYPE_DIALOGUE_HAS_STARTED:
          $dialogue = Dialogue::get_by_id(
            pdo: $app->get_database(),
            id: $this->dialogue_id
          );
          ?>
          <h4>Dialogue has started!</h4>
          <p><?= $dialogue->get_title($app) ?></p>
          <?php
          break;

      endswitch;
      ?>
    </div>
    <?php
    return ob_get_clean();
  }
}