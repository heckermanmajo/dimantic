<?php

namespace cls\data\league;

use cls\DataClass;

/**
 * If an account rates a post match, this is the db entry created.
 */
class PostMatchRating extends DataClass {
  var int $post_match_id = 0;
  var int $account_id = 0;
  /**
   * Comma seperated list of the ratings, for each rating-dimension.
   * f.e.  "24:1, 245:2, 23:1, ..."
   * <id_of_rating_dimension_entry>:<1_or_2>, ...
   */
  var string $ratings = "";
  var string $comment = "";
}