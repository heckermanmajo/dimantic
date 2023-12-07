<?php

namespace cls\data\group;

/**
 * Specific member-rights can be set in the membership.
 * Also roles can be assigned to members.
 *
 * -> membership also contains the settings what infos
 *    you get as news entry.
 */
class GroupMembership {

  const INVITATION = 1;
  const REQUEST = 2;
  const MEMBER = 3;

  /**
   * The message can be the message from the invitor to the invitee
   * or the message from the requester to the group admin.
   *
   * -> you can only join a group, not a room.
   *
   * @var string
   */
  public string $message = '';
}