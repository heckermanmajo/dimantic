<?php

namespace cls\controller\request\rating;

use App;
use cls\controller\algo\RatingController;
use cls\data\AttentionDimensionEntry;
use cls\data\post\Post;
use cls\data\RateHistoryEntry;
use cls\RequestError;

class InsertRelativeRating {
  static function execute(
    int $rating_history_entry_id,
    int $rating_value,
  ): RequestError|RateHistoryEntry {

    $rating_history_entry = RateHistoryEntry::get_by_id(
      App::get_connection(),
      $rating_history_entry_id
    );

    if ($rating_history_entry == null) {
      return new RequestError(
        "Rating history entry not found.",
        RequestError::NOT_FOUND,
        extra_data: [
          "rating_history_entry_id" => $rating_history_entry_id
        ]
      );
    }

    # todo: check that it is 1 or 2
    $rating_history_entry->rating_value = $rating_value;

    $post_id = $rating_history_entry->post_id;
    $other_post_id = $rating_history_entry->other_post_id;
    $post = Post::get_by_id(App::get_connection(), $post_id);
    $other_post = Post::get_by_id(App::get_connection(), $other_post_id);

    $post_dimension_entry = AttentionDimensionEntry::get_one(
      App::get_connection(),
      "SELECT * FROM `AttentionDimensionEntry` WHERE `post_id` = ? AND `attention_dimension_id` = ?;",
      [
        $post->id,
        $rating_history_entry->attention_dimension_id
      ]
    );

    $other_post_dimension_entry = AttentionDimensionEntry::get_one(
      App::get_connection(),
      "SELECT * FROM `AttentionDimensionEntry` WHERE `post_id` = ? AND `attention_dimension_id` = ?;",
      [
        $other_post->id,
        $rating_history_entry->attention_dimension_id
      ]
    );

    RatingController::apply_points_after_rating_to_dimension_entries(
      $post_dimension_entry,
      $other_post_dimension_entry,
      $rating_value, // 1 or 2
      App::get_current_account()
    );

    $rating_history_entry->save(App::get_connection());

    return $rating_history_entry;

  }
}