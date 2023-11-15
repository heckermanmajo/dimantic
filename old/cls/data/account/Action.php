<?php

namespace cls\data\account;

use cls\DataClass;

/**
 * Statistically log of member action.
 */
class Action extends DataClass {
  var string $action_name = "";
  var int $account_id = 0;
}