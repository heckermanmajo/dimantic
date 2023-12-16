<?php

namespace cls\data\space;

class SpaceMembership {
  /**
   * Consul are the owner of the space.
   * The can do everything and remove and create other roles.
   */
  const ROLE_CONSUL = 1;
  /**
   * Tribun are the moderators of the space selected by their
   * contribution to the space.
   * They can be dismissed by the consul. (maybe more complex process)
   */
  const ROLE_TRIBUN = 2;
  /**
   * Ministers are just selected by the consul.
   * BUT the consul has direct responsibility for the ministers.
   */
  const ROLE_MINISTER = 3;
  /**
   * Members are just members.
   * They can be promoted to ministers by the consul.
   */
  const ROLE_MEMBER = 4;
  /**
   * Guests are just guests.
   * They can be promoted to members by the consul or
   * increase the rank if they contribute to the space.
   */
  const ROLE_GUEST = 5;
}