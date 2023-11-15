<?php

namespace cls\controller\algo;

use App;
use cls\data\AttentionDimensionEntry;
use cls\data\post\Post;

class PostVirtue {

  /**
   * This request is used to generate the virtue value for a post.
   * It does not change any data in the database.
   * The virtue represents the overall quality of a post.
   *
   * @param Post $post
   * @param int $number_of_posts_when_to_count_as_upper_score
   * @return int
   */
  static function generate_virtue_value_for_post_based_on_rating_values_of_its_entries(
    Post $post,
    int  $number_of_posts_when_to_count_as_upper_score = 300
  ): int {

    # select all attention dimension entries
    $attention_dimension_entries_of_post = AttentionDimensionEntry::get_array(
      App::get_connection(),
      "SELECT * FROM `AttentionDimensionEntry` WHERE `post_id` = ?;",
      [
        $post->id
      ]
    );

    $number_of_all_dimensions = count($attention_dimension_entries_of_post);

    # get for each dimension of competition the number of entries
    # that this post competes with
    $attention_dimension_entries_mapped_on_data = [];
    foreach ($attention_dimension_entries_of_post as $attentionDimensionEntry) {
      $absolute_number_of_entries_in_dimension = AttentionDimensionEntry::get_count(
        App::get_connection(),
        "SELECT COUNT(*) FROM `AttentionDimensionEntry` WHERE `attention_dimension_id` = ?;",
        [
          $attentionDimensionEntry->attention_dimension_id
        ]
      );
      $attention_dimension_entries_mapped_on_data[$attentionDimensionEntry->id] = [
        "absolute_number_of_entries_in_dimension" => $absolute_number_of_entries_in_dimension,
        "position_in_dimension" => self::get_absolute_postion_value_of_entry_within_dimension($attentionDimensionEntry),
      ];
    }

    $virtue_points = 0;
    $upper_scores = 0;

    foreach ($attention_dimension_entries_mapped_on_data as $data) {

      $virtue_points += self::get_points_per_dimension(
        $data["position_in_dimension"],
        $data["absolute_number_of_entries_in_dimension"]
      );

      $position_percentage = $data["position_in_dimension"] / $data["absolute_number_of_entries_in_dimension"];
      # increase upper scores if we are in the upper 20% of a dimension
      if ($position_percentage > 0.8) {
        # only count beyond certain amount (on default 300) entries, otherwise it is not a "real" upper score
        if ($data["absolute_number_of_entries_in_dimension"] > $number_of_posts_when_to_count_as_upper_score) {
          $upper_scores++;
        }
      }
    }

    # todo: extract mre specific density-value, and use density to upgrade te virtue
    # The more dimensions the post is in the upper 20% the more virtue points it gets
    # exponential -> since it is exponentially more difficult to be in the upper 20% in multiple dimensions
    # This bonus optimizes therefore for "density" of value "Dichte"
    if ($upper_scores > 2) {
      $virtue_points = $virtue_points ** (1  + ($upper_scores / 50));
      # 3 dimensions upper 20% -> virtue_pints ** 1.3
      # 4 dimensions upper 20% -> virtue_pints ** 1.4
      # 5 dimensions upper 20% -> virtue_pints ** 1.5
      # 6 dimensions upper 20% -> virtue_pints ** 1.6
      # 7 dimensions upper 20% -> virtue_pints ** 1.7
      # etc.
    }

    return floor($virtue_points);
  }

  static function get_points_per_dimension(
    int $position_in_dimension,
    int $absolute_number_of_entries_in_dimension,
  ): int {

    # this cannot happen since we only call this function
    # if at least one entry is in the dimension -> otherwise we wouldnt have an entry
    # so this is just fucked-up-big-time save guard
    App::assert($absolute_number_of_entries_in_dimension > 0,"Fucked up big time, this shouldn't happen at all");

    # this also shouldn't happen
    App::assert($position_in_dimension > 0, "Fucked up big time, this shouldn't happen at all");

    # position: bigger is better, since higher postion means more points
    $position_percentage = $position_in_dimension / $absolute_number_of_entries_in_dimension;
    # best position_percentage is 1 -> 100%
    # position_percentage is 0.99 -> 1%
    # position_percentage is 0.5 -> 50%
    # worst position_percentage is 0 -> 0%
    $position_percentage = 1 - $position_percentage;

    if ($absolute_number_of_entries_in_dimension > 100) { #  more posts than 100
      $standard_value = 0;
      if ($position_percentage <= 0.01) {
        $standard_value = 200;
      }
      elseif ($position_percentage <= 0.05) {
        $standard_value = 100;
      }
      elseif ($position_percentage <= 0.2) {
        $standard_value = 50;
      }
      elseif ($position_percentage <= 0.6) {
        $standard_value = 25;
      }
      elseif ($position_percentage <= 0.8) {
        $standard_value = 10;
      }
      else {
        // it is already 0
      }
      # apply size bonus to rating value
      # since if more posts compete in the dimension
      # we want a higher percentage to be much more valuable
      $standard_value = $standard_value * $absolute_number_of_entries_in_dimension / 100;

      return $standard_value;
    }
    else {
      # if we have less than 100 entries
      # then as check which position we are in
      # since all positions we can be been smaller than 100
      # we don't need any percentage calculation
      # we also don't need any size bonus
      if ($position_in_dimension == 100) {
        return 200;
      }
      elseif ($position_in_dimension > 95) {
        return 100;
      }
      elseif ($position_in_dimension > 80) {
        return 50;
      }
      elseif ($position_in_dimension > 40) {
        return 25;
      }
      elseif ($position_in_dimension > 20) {
        return 10;
      }
      else {
        return 0;
      }
    }
  }

  static function get_absolute_postion_value_of_entry_within_dimension(
    AttentionDimensionEntry $attentionDimensionEntry
  ): int {
    #echo $attentionDimensionEntry->position_points / $attentionDimensionEntry->number_of_ratings . "\n";
    return AttentionDimensionEntry::get_count(
      App::get_connection(),
      "SELECT COUNT(*)+1 FROM `AttentionDimensionEntry`
                WHERE `attention_dimension_id` = ? 
                  AND (AttentionDimensionEntry.`position_points`*1.0)/(AttentionDimensionEntry.number_of_ratings*1.0) < (?*1.0);",
      [
        $attentionDimensionEntry->attention_dimension_id,
        $attentionDimensionEntry->position_points / $attentionDimensionEntry->number_of_ratings
      ]
    );
  }
}