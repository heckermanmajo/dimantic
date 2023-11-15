<?php

namespace cls\controller\request\attention_league;

use cls\data\league\LeagueRatingDimension;
use cls\RequestError;

class RemoveRatingDimensionFromLeague {
  static function execute(): RequestError|null {
    $attention_league_id = $_POST["attention_league_id"];
    $attention_dimension_id = $_POST["attention_dimension_id"];
    # todo: check for admin and correct input values ...

    $league_rating_dimension = LeagueRatingDimension::get_one(
      \App::get_connection(),
      "SELECT * FROM LeagueRatingDimension WHERE attention_league_id = :attention_league_id AND attention_dimension_id = :attention_dimension_id",
      [
        "attention_league_id" => $attention_league_id,
        "attention_dimension_id" => $attention_dimension_id
      ]
    );

    $league_rating_dimension->delete(\App::get_connection());

    return null;
  }
}