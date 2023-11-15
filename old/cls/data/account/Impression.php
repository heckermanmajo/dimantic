<?php

namespace cls\data\account;

use cls\DataClass;

/**
 * Who has seen what?
 * -> this allows to caculate how many people have seen a post/tree
 * and can be used to get information for advertising.
 */
class Impression extends DataClass {
  var int $member_id = 0;
  var int $post_id = 0;
  var int $tree_id = 0;
  var int $number = 0;
  var string $create_date = "";
}