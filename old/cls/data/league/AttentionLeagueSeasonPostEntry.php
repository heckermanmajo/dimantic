<?php

namespace cls\data\league;

use cls\DataClass;

/**
 * Created if a user posts an entry to a league season.
 */
class AttentionLeagueSeasonPostEntry extends DataClass {
  var int $attention_league_season_id = 0;
  var int $post_id = 0;
  var string $created_date = "";
}