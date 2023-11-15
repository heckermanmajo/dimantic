<?php

namespace cls\data\idea_space;

use cls\DataClass;

class IdeaSpaceMembership extends DataClass {
  var int $idea_space_id = 0;
  var int $account_id = 0;
  var int $left_idea_space = 0;

  #var string $membership_state = "";
  #var string $application_text = "";
  #var string $created_date = "";
  #var int $idea_space_related_competence = 0;
  #var int $idea_space_related_trust = 100;
}