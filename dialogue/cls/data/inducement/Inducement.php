<?php

namespace cls\data\inducement;

/**
 * Initiation of a conversation.
 */
class Inducement {

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
   * @var int Inducement is active, means it is open.
   */
  public int $active = 0;


  # todo: all the rules texts
  # todo: the question text
  # todo: how many users
  # todo: how long a message, how many messages, how the timing
}