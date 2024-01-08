<?php

namespace cls\data\conversation_blue_print;

use cls\App;
use cls\data\account\Account;
use cls\DataClass;
use cls\GetDisplayCardInterface;
use cls\MarkdownUtils;
use Exception;

/**
 * Initiation of a conversation.
 */
class ConversationBluePrint extends DataClass implements GetDisplayCardInterface {

  public int $author_id = 0;

  public int $space_id = 0;

  /**
   * 1 if this is a counter offer to another ConversationBluePrint.
   */
  public int $is_counter_offer = 0;
  /**
   * If you create a counter offer, you first need to set it to
   * offered, so that you have time after creating an alternative
   * description to also change some rules.
   *
   * Since the rules are other data-classes, we need to
   * not display the counter offer after creation of the counter offer
   * itself, since the creator of the counter offer had no chance
   * to change the rules yet.
   *
   * @var int
   */
  public int $is_offered = 0;

  public int $published = 0;
  public int $unpublish_after_number_of_days = 0;
  public int $unpublish_after_number_of_created_conversations = 0;

  /**
   * The id of the inducement, this one is the counteroffer to.
   * I want to talk to you, since I have found your inducement
   * but some settings are not fine I think, so I copy your inducement
   * and change some settings, and add a message, after this you can cone mine
   * and add a message, until we can start talking.
   */
  public int $counteroffer_blueprint_id = 0;

  /**
   * The message of the counteroffer.
   */
  public string $counteroffer_message = '';

  /**
   * @var int The number of messages that are allowed in a sub-dialogue.
   */
  public int $sub_discussion_depth = 0;

  /**
   * @var int how many levels of sub discussions are allowed.
   * MainMessage 0
   *  SubMessage 1
   *    SubSubMessage 2
   *      SubSubSubMessage 3
   * etc.
   *
   * -1 means unlimited.
   * 0 means no sub discussions at all.
   */
  public int $sub_discussion_width = 0;

  /**
   * @var int Inducement is active, means it is open.
   */
  public int $active = 0;

  /**
   * @var string The description of the inducement - not the
   * topic of the dialogue, but text about this blueprint.
   */
  public string $description = '';

  /**
   * @var string The topic of the dialogue that should be started.
   */
  public string $topic_dialogue_description = '';

  public int $min_number_of_users = 2;
  public int $max_number_of_users = 2;
  public int $min_number_of_messages = 2;
  public int $max_number_of_messages = 2;
  public int $min_message_length_chars = 2;
  public int $max_message_length_chars = 2;
  public int $min_minutes_between_answers = 2;
  public int $max_minutes_between_answers = 2;

  public float $like_percentage = 0.1;

  public float $number_summaries_per_message_per_user = 0.2;
  /**
   * You can invite somebody, who can write an opinion about the dialogue.
   * and ist removed afterward.
   */
  public int $allow_invite_opinions = 0;
  public int $number_of_allowed_invite_opinions = 0;
  public int $has_moderator = 0;


  public static function getDefaultConfigurationDialogue(): ConversationBluePrint {
    $bp = new ConversationBluePrint();

    $bp->min_number_of_users = 2;
    $bp->max_number_of_users = 2;

    $bp->min_number_of_messages = -1;
    $bp->max_number_of_messages = -1;

    $bp->min_message_length_chars = -1;
    $bp->max_message_length_chars = -1;

    $bp->min_minutes_between_answers = 2;
    $bp->max_minutes_between_answers = 2;

    $bp->like_percentage = 0.01;

    $bp->number_summaries_per_message_per_user = 0.2;

    $bp->allow_invite_opinions = 0;

    $bp->number_of_allowed_invite_opinions = 0;

    $bp->has_moderator = 0;

    $bp->unpublish_after_number_of_created_conversations = 2;

    $bp->unpublish_after_number_of_days = 10;

    return $bp;
  }

  /**
   * If a blueprint is used to create a conversation, it is in use.
   * - if a blueprint is in use, it cannot be changed.
   *
   * -> so if this function returns true. you need to clone the blueprint in order
   *    to change it.
   *
   * -> only thing you can change is the publication state and the
   *   number of days it is published and the number of conversations
   *   after which it is unpublished.
   *
   * @return bool
   * @throws Exception
   */
  function is_in_use(): bool {
    return ConversationBluePrint::get_count(
        pdo: App::get()->get_database(),
        sql: "SELECT COUNT(*) FROM `Dialogue` WHERE `blue_print_id` = ?",
        params: [$this->id]
      ) > 0;
  }

  /**
   * @throws Exception
   */
  function get_display_card(): string {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

    if (
      !$this->user_is_allowed_to_see_blueprint(
        user_id: App::get()->get_currently_logged_in_account()->id
      )
    ) {
      $warn("User is not allowed to see blueprint, but display function was called, so has he some access???");
      return '<div class="sketch-card w3-margin"> You are not allowed to see this blueprint. </div>';
    }

    # todo: improve ...
    $author_account = Account::get_by_id(
      App::get()->get_database(),
      $this->author_id
    );


    ob_start();
    ?>
    <div class="sketch-card w3-margin">
      <div>
        <b style="color: #3f51b5">Conversation Blueprint</b>
        by <b>
          <?= $author_account->name ?>
        </b> -
        <?= ($this->published == 1) ? "<b>Published</b>" : "NOT published" ?>
        <div class="w3-right">
          <?= $author_account->get_gravtar_profile_image() ?>
        </div>
      </div>

      <div class="w3-container">


        <!-- If you have received a counter-offer, you can see it here. -->
        <!--<div class="w3-padding">
          <i>
            📜 You have received an COUNTER-OFFER on this blueprint.
          </i>
          <ul>
            <li>
              Link to the counter offer 1
            </li>
            <li>
              Link to the counter offer 2
            </li>
          </ul>
        </div>-->

        <!-- Number of JOINED  members: who has said: yes lets start talking -->
          <!--<pre><?php var_dump($this)?></pre>-->
        <a href="/blueprint.php?id=<?= $this->id ?>">
          <h3><?= MarkdownUtils::get_title_from_md_content($this->description) ?></h3>
        </a>

        <p><?= App::get()->markdown_to_html(MarkdownUtils::get_md_content_without_title($this->description)) ?></p>

        <p>Number of lobbies: <?=Lobby::get_number_of_lobbies_for_blueprint(
          blueprint_id: $this->id
          )?></p>

      </div>

      <!--<div>
        <button> Clone this blueprint</button>
      </div>-->

      <pre style="display: none"><?= json_encode($this, JSON_PRETTY_PRINT) ?></pre>


    </div>
    <?php
    return ob_get_clean();
  }
  
  /**
   * @param int $user
   * @param int $space_id
   * @param string $search_string
   * @return array<ConversationBluePrint>
   * @throws Exception
   */
  static function search_by_search_text_in_space(
    int    $user,
    int    $space_id,
    string $search_string,
  ): array {

    return static::get_array(
      pdo: App::get()->get_database(),
      sql: "
            SELECT * FROM ConversationBluePrint 
                WHERE 
                    space_id = ? 
                    AND (
                        /* Display mine or the offerings of other people that are published */
                        author_id = ? OR published = 1
                    )
                    AND description LIKE ?",
      params: [
        $space_id,                 # [0]
        $user,                     # [1]
        '%' . $search_string . '%' # [2]
      ],
      fields_to_not_escape: [2]
    );

  }

  function user_is_allowed_to_publish_blueprint(
    int $user_id
  ): bool {
    return $this->author_id === $user_id;
  }

  function user_is_allowed_to_unpublish_blueprint(
    int $user_id
  ): bool {
    return $this->author_id === $user_id;
  }

  function user_is_allowed_to_edit_blueprint(
    int $user_id
  ): bool {
    return $this->author_id === $user_id;
  }

  function user_is_allowed_to_see_blueprint(
    int $user_id
  ): bool {

    if ($this->published === 1) {
      return true;
    }

    if ($this->given_user_is_invited($user_id)) {
      return true;
    }

    return $this->author_id === $user_id;
  }

  function given_user_is_invited(
    int $user_id
  ): bool {
    # todo: implement ...
    return false;
  }

  function user_is_allowed_to_create_lobby(int $user_id): bool {
    return $this->author_id === $user_id;
  }

  /**
   * Retrieve all published blueprints for a given space.
   *
   * @param int $space_id The ID of the space for which to retrieve the blueprints.
   *
   * @return array<static> An array containing all published blueprints for the given space.
   *
   * @throws Exception If an error occurs while retrieving the blueprints.
   */
  static function get_all_published_blueprints(
    int $space_id
  ): array {
    return static::get_array(
      pdo: App::get()->get_database(),
      sql: "SELECT * FROM ConversationBluePrint 
         WHERE published = 1 AND space_id = ?",
      params: [$space_id]
    );
  }

}