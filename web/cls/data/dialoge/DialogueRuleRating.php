<?php

namespace cls\data\dialoge;

use cls\DataClass;

class DialogueRuleRating extends DataClass {
  var int $dialogue_rule_id = 0;
  var int $rating = 0;
  var int $account = 0;
  /**
   * @var string If the user rejects the rule, he can give a reason.
   */
  var string $reason_text = '';

  const RATING_PENDING = 0;
  const RATING_ACCEPT = 1;
  const RATING_REJECT = 2;
}