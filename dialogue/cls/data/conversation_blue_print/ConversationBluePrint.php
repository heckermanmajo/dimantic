<?php

namespace cls\data\conversation_blue_print;

use cls\App;
use cls\DataClass;
use Exception;

/**
 * Initiation of a conversation.
 */
class ConversationBluePrint extends DataClass {

  public int $author_id = 0;

  public int $space_id = 0;

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
   * @param App $app
   * @return bool
   * @throws Exception
   */
  function is_in_use(App $app): bool {
    return ConversationBluePrint::get_count(
      pdo: $app->get_database(),
      sql: "SELECT COUNT(*) FROM `Dialogue` WHERE `blue_print_id` = ?",
      params: [$this->id]
    ) > 0;
  }
  
}