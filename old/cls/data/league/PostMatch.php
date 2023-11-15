<?php

namespace cls\data\league;

use App;
use cls\data\post\Post;
use cls\DataClass;

/**
 * Posts that compete in a league can be
 * matched against each other.
 */
class PostMatch extends DataClass {
  var int $post_1_id = 0;
  var int $post_2_id = 0;
  var int $season_id = 0;

  /**
   * Returns an array with two values; Post id1 mapped on score  and
   * Post id2 mapped on score.
   * @return array<int, int>
   */
  function get_relative_scores(): array { }

  function get_current_winner(): Post { }

  function get_number_of_ratings(): int { }

  function get_post_1(): Post {
    return Post::get_by_id(App::get_connection(), $this->post_1_id);
  }

  function get_post_2(): Post {
    return Post::get_by_id(App::get_connection(), $this->post_2_id);
  }

  function get_season(): AttentionLeagueSeason {
    return AttentionLeagueSeason::get_by_id(App::get_connection(), $this->season_id);
  }

  function get_league(): AttentionLeague {
    return $this->get_season()->get_attention_league();
  }

  function get_attention_dimensions(): array {
    return $this->get_league()->get_rating_dimensions();
  }


}