<?php

namespace cls\data\league;

use App;
use cls\data\league;
use cls\data\post\Post;
use cls\DataClass;

class AttentionLeague extends DataClass {
  /**
   * Description of league.
   */
  var string $league_description = "";
  /**
   * Number of days from the creation of a season until it can start the rating
   * process. The rating process cannot be started if there are not enough posts.
   */
  var int $days_per_season = 0;
  /**
   * The max number of posts one idea space can submit to the league.
   */
  var int $max_number_of_posts_per_season_per_idea_space = 0;
  /**
   * Number of posts needed to start the rating process.
   * If this number is not reached, the rating process cannot be started.
   * If the time is not yet reached, the rating process cannot be started.
   */
  var int $number_of_minimum_posts_per_season = 0;
  /**
   * The minimum number of ratings of any post match of posts competing in the
   * rating stage of the league.
   * If one post is not rated enough, the league will not be closed.
   */
  var int $min_number_of_in_league_ratings_of_post_match = 0;
  var string $created_date = "";
  var int $number_of_seasons = 0;

  function get_title(): string {
    # todo: this is a temporary solution, use interpreter instead
    return $this->league_description;
  }

  /**
   * Creates a new season.
   *
   * @return AttentionLeagueSeason
   */
  function create_new_season(): AttentionLeagueSeason {

    // check if no season exists
    $seasons = AttentionLeagueSeason::get_array(
      App::get_connection(),
      "SELECT * FROM `AttentionLeagueSeason` 
         WHERE attention_league_id = ? AND state='open' ORDER BY id DESC LIMIT 1;",
      [$this->id]
    );

    foreach ($seasons as $season) {
      $season->state = "closed";
    }

    if(count($seasons) > 0){
      echo "Season already exists";
      # todo: what we do here is based on the usage, determined later ...
    }

    $season = new AttentionLeagueSeason();
    $season->attention_league_id = $this->id;
    $season->season_description = "Season " . $this->number_of_seasons+1;
    $season->season_start_date = date("Y-m-d H:i:s");
    $season->state = "open";
    $season->save(App::get_connection());

    $this->number_of_seasons++;
    $this->save(App::get_connection());

    return $season;

  }

  function get_latest_season(): AttentionLeagueSeason {
    return AttentionLeagueSeason::get_one(
      App::get_connection(),
      "SELECT * FROM `AttentionLeagueSeason` 
         WHERE attention_league_id = ? ORDER BY id DESC LIMIT 1;",
      [$this->id]
    );
  }

  /**
   * @return array<AttentionDimension>
   */
  function get_rating_dimensions(): array {
    return league\AttentionDimension::get_array(
      App::get_connection(),
      "SELECT * FROM `AttentionDimension` WHERE id NOT IN 
            (SELECT attention_dimension_id FROM LeagueRatingDimension WHERE attention_league_id = ?);",
      [$this->id]
    );
  }


  /**
   * Starts the rating process of a league-season.
   *
   * Usually called by a cron job, or in the admin window.
   * @return void
   * @throws \Exception
   */
  function start_league_rating_and_create_matches(): void {

    $season = $this->get_latest_season();

    if($season->can_change_state_from_open_to_rating()){
      $season->state = "rating";
      $season->save(App::get_connection());
      App::$logs[] = "Season state changed to rating";

    } else {
      throw new \Exception("Season cannot be started");
    }

    # create matches between posts
    # how many matches?
    $number_of_posts = $season->get_number_of_posts_of_season();

    if($number_of_posts < 4) {
      $matches_per_post = $number_of_posts - 1;
    }elseif($number_of_posts < 10){
      $matches_per_post = 3;
    }else{
      $matches_per_post = round($number_of_posts * 0.3);
    }

    $all_posts = $season->get_qualified_posts();

    foreach ($all_posts as $post) {

      $match_partner = [];

      for ($i = 0; $i<$matches_per_post; $i++){

        while($possible_match_partner = $all_posts[array_rand($all_posts)]){
          if($possible_match_partner->id != $post->id && !in_array($possible_match_partner->id, $match_partner)){
            break;
          }
        }

        $match_partner[] = $possible_match_partner->id;

        $match = new PostMatch();
        $match->post_1_id = $post->id;
        $match->post_2_id = $possible_match_partner->id;
        $match->save(App::get_connection());
      }

    }


  }

}