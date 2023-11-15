<?php

namespace cls\controller\request\attention_league;

use cls\data\league\AttentionLeague;
use cls\RequestError;

class CreateAttentionLeague {
  static function execute(): RequestError|AttentionLeague {
    #var string $league_description = "";
    #var int $days_per_season = 0;
    #var int $number_of_posts_per_season = 0;
    if (!isset($_POST["league_description"])) {
      return new RequestError(
        "Error while creating attention league: League description is missing.",
        RequestError::BAD_REQUEST
      );
    }

    if (!isset($_POST["days_per_season"])) {
      return new RequestError(
        "Error while creating attention league: Days per season is missing.",
        RequestError::BAD_REQUEST
      );
    }

    if (!isset($_POST["number_of_posts_per_season"])) {
      return new RequestError(
        "Error while creating attention league: Number of posts per season is missing.",
        RequestError::BAD_REQUEST
      );
    }

    # todo: escape and check ....
    $league_description = $_POST["league_description"];
    $days_per_season = $_POST["days_per_season"];
    $number_of_posts_per_season = $_POST["number_of_posts_per_season"];

    $attention_league = new AttentionLeague();
    $attention_league->league_description = $league_description;
    $attention_league->days_per_season = $days_per_season;
    $attention_league->number_of_posts_per_season = $number_of_posts_per_season;
    $attention_league->created_date = date("Y-m-d H:i:s");

    $attention_league->save(\App::get_connection());

    return $attention_league;
  }
}