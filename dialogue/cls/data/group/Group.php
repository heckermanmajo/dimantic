<?php

namespace cls\data\group;

class Group {
  public int $parent_group = 0;

  /**
   * If this group has a parent group, it is a room.
   * @return bool
   */
  public function is_room(): bool {
    return $this->parent_group > 0;
  }
}