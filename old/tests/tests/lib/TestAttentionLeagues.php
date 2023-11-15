<?php

namespace tests\tests\lib;

class TestAttentionLeagues {
  static function create(){

    $attention_league = new \cls\data\league\AttentionLeague();
    $attention_league->league_description = "Demografischer Wandel";
    $attention_league->days_per_season = 0;
    $attention_league->max_number_of_posts_per_season_per_idea_space = 3;
    $attention_league->number_of_minimum_posts_per_season = 10;
    $attention_league->created_date = "2020-01-01 00:00:00";
    $attention_league->save(\App::get_connection());
    $attention_league->create_new_season();

    $attention_league = new \cls\data\league\AttentionLeague();
    $attention_league->league_description = "Deutsche Energiearmut";
    $attention_league->days_per_season = 30;
    $attention_league->number_of_minimum_posts_per_season = 10;
    $attention_league->max_number_of_posts_per_season_per_idea_space = 3;
    $attention_league->created_date = "2020-01-01 00:00:00";
    $attention_league->save(\App::get_connection());
    $attention_league->create_new_season();

    $attention_league = new \cls\data\league\AttentionLeague();
    $attention_league->league_description = "Überkomplexität von Institutionen";
    $attention_league->days_per_season = 7;
    $attention_league->number_of_minimum_posts_per_season = 10;
    $attention_league->max_number_of_posts_per_season_per_idea_space = 3;
    $attention_league->created_date = "2020-01-01 00:00:00";
    $attention_league->save(\App::get_connection());
    $attention_league->create_new_season();

    $attention_league = new \cls\data\league\AttentionLeague();
    $attention_league->league_description = "De-Industrialisierung";
    $attention_league->days_per_season = 14;
    $attention_league->number_of_minimum_posts_per_season = 10;
    $attention_league->max_number_of_posts_per_season_per_idea_space = 3;
    $attention_league->created_date = "2020-01-01 00:00:00";
    $attention_league->save(\App::get_connection());
    $attention_league->create_new_season();

    $attention_league = new \cls\data\league\AttentionLeague();
    $attention_league->league_description = "Deglobalisierung";
    $attention_league->days_per_season = 14;
    $attention_league->number_of_minimum_posts_per_season = 10;
    $attention_league->max_number_of_posts_per_season_per_idea_space = 3;
    $attention_league->created_date = "2020-01-01 00:00:00";
    $attention_league->save(\App::get_connection());
    $attention_league->create_new_season();
  }
}