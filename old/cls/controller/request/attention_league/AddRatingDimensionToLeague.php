<?php

namespace cls\controller\request\attention_league;

use cls\data\league\LeagueRatingDimension;
use cls\RequestError;

class AddRatingDimensionToLeague {
  static function execute(): RequestError|LeagueRatingDimension {
    $attention_league_id = $_POST["attention_league_id"];
    $attention_dimension_id = $_POST["attention_dimension_id"];
    $relevance_multiplier = $_POST["relevance_multiplier"];

    # todo: check for admin and correct input values ...

    $league_rating_dimension = new \cls\data\league\LeagueRatingDimension();
    $league_rating_dimension->attention_dimension_id = $attention_dimension_id;
    $league_rating_dimension->attention_league_id = $attention_league_id;
    $league_rating_dimension->relevance_multiplier = $relevance_multiplier;
    $league_rating_dimension->save(\App::get_connection());

    return $league_rating_dimension;
  }
}