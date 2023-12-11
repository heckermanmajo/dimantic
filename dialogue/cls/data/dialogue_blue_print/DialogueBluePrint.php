<?php

namespace cls\data\dialogue_blue_print;

use cls\DataClass;

/**
 * Initiation of a conversation.
 */
class DialogueBluePrint extends DataClass {

  public int $author = 0;

  /**
   * The id of the inducement, this one is the counteroffer to.
   * I want to talk to you, since I have found your inducement
   * but some settings are not fine I think, so I copy your inducement
   * and change some settings, and add a message, after this you can cone mine
   * and add a message, until we can start talking.
   */
  public int $counteroffer_id_inducement = 0;

  /**
   * The message of the counteroffer.
   */
  public string $counteroffer_message = '';

  /**
   * @var int Inducement is part of a group.
   */
  public int $belongs_to_group = 0;

  /**
   * @var int Inducement is part of a user.
   */
  public int $belongs_to_user = 0;

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
   * @var string Array of proto rules as json.
   * Just a list of strings of markdown.
   */
  public string $proto_rules_as_json = '[]';

  /**
   * @var string The description of the inducement - not the
   * topic of the dialogue, but text about this blueprint.
   */
  public string $blueprint_description = '';
  
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


  public static function getDefaultConfigurationDialogue(): DialogueBluePrint {
    $bp = new DialogueBluePrint();

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

    return $bp;
  }
  
}