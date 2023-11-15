<?php

namespace cls\data\league;

use cls\DataClass;

/**
 * Entry that associates a rating dimension with a league.
 */
class LeagueRatingDimension extends DataClass {
  var int $attention_dimension_id = 0;
  var int $attention_league_id = 0;
  var int $relevance_multiplier = 1;
}