<?php

namespace cls\controller\algo;

use App;
use cls\data\account\Account;
use cls\data\post\Post;

class RatingController {

  /**
   * @param Post $post
   * @return array<AttentionDimensionEntry>
   * @throws \Exception
   */
  static function get_attention_dimension_entries_the_current_user_can_rate_on(Post $post): array {

    # no if I am the author
    if (App::get_current_account()->id == $post->author_id) {
      return [];
    }

    return AttentionDimensionEntry::get_array(
      App::get_connection(),
      "
       SELECT * FROM AttentionDimensionEntry
        WHERE AttentionDimensionEntry.attention_dimension_id NOT IN (
            SELECT RateHistoryEntry.attention_dimension_id FROM RateHistoryEntry
              WHERE RateHistoryEntry.account_id = :account_id
              AND RateHistoryEntry.post_id = :post_id
              AND RateHistoryEntry.rating_value != 0
        ) AND AttentionDimensionEntry.post_id = :post_id;
    ",
      [
        "account_id" => App::get_current_account()->id,
        "post_id" => $post->id
      ]
    );
  }

  static function get_posts_dimension_entries_by_dimension_based_on_rating(
    int $limit,
    int $max_dimension_rating_value
  ): array {
    return AttentionDimensionEntry::get_array(
      App::get_connection(),
      "
      SELECT 
          *, 
          (`position_points`/`number_of_ratings`) as _dimension_rating_value 
        FROM AttentionDimensionEntry
            WHERE number_of_ratings > 10
            AND (`position_points`/`number_of_ratings`) < :max_dimension_rating_value
        ORDER BY _dimension_rating_value DESC LIMIT :limit;
    ",
      [
        "max_dimension_rating_value" => $max_dimension_rating_value
      ],
    );
  }

  static function get_rating_partner_post_attention_dimension_entry(
    AttentionDimensionEntry $partner_searching_attention_dimension_entry,
    int                     $padding_in_points = 10,
    int                     $percentage_trigger_for_random_partner = -1
  ): AttentionDimensionEntry {

    $padding_in_points = 10;
    $random_percentage = rand(0, 100);

    if ($random_percentage > $percentage_trigger_for_random_partner) {
      $partner_post = AttentionDimensionEntry::get_one(
        App::get_connection(),
        "
        SELECT *,
          (`position_points`/`number_of_ratings`) as _dimension_rating_value,
          (SELECT Post.author_id FROM Post WHERE AttentionDimensionEntry.post_id = Post.id) as _author_of_post_id
        FROM AttentionDimensionEntry
          WHERE 
              /*number_of_ratings > 10
          AND*/ (`position_points`/`number_of_ratings`) > (:search_entry_dimension_rating_value - :padding_in_points)
          AND (`position_points`/`number_of_ratings`) < (:search_entry_dimension_rating_value + :padding_in_points)
          AND post_id != :post_id 
          AND _author_of_post_id != :author_id
          ORDER BY RANDOM() LIMIT 1;
          ",
        [
          "search_entry_dimension_rating_value" => $partner_searching_attention_dimension_entry->_dimension_rating_value,
          "padding_in_points" => $padding_in_points,
          "post_id" => $partner_searching_attention_dimension_entry->post_id,
          "author_id" => App::get_current_account()->id
        ]
      );
    }

    else { # get random one

      $partner_post = AttentionDimensionEntry::get_one(
        App::get_connection(),
        "
        SELECT *,
          (`position_points`/`number_of_ratings`) as _dimension_rating_value 
        FROM AttentionDimensionEntry
           WHERE /*
              number_of_ratings > 10
          AND*/ post_id != :post_id
          ORDER BY RANDOM() LIMIT 1;
          ",
        [
          "post_id" => $partner_searching_attention_dimension_entry->post_id
        ]
      );
    }

    return $partner_post;
  }


  static function apply_points_after_rating_to_dimension_entries(
    AttentionDimensionEntry $post_dimension_entry,
    AttentionDimensionEntry $other_post_dimension_entry,
    int $rating_value, // 1 or 2
    Account $rating_user #  todo: make the competence of the user a factor in the amount of points
  ): void {

    #todo move this into an algorithmic controller
    $post_rating_delta = abs(
      $post_dimension_entry->position_points / $post_dimension_entry->number_of_ratings
      - $other_post_dimension_entry->position_points / $other_post_dimension_entry->number_of_ratings
    );
    $post_has_more_position_points = $post_dimension_entry->position_points / $post_dimension_entry->number_of_ratings
      > $other_post_dimension_entry->position_points / $other_post_dimension_entry->number_of_ratings;

    if ($rating_value == 1) { # increase the value of post 1
      if ($post_has_more_position_points) { # you can gain up to 3 points
        $post_dimension_entry->position_points += 1;
      }
      else {
        # todo: maybe dont hard code the values and have a algo-function to calculate the points
        if ($post_rating_delta < 3) {
          $post_dimension_entry->position_points += 1;
        }
        elseif ($post_rating_delta < 6) {
          $post_dimension_entry->position_points += 2;
        }
        else {
          $post_dimension_entry->position_points += 3;
        }
      }
      $post_dimension_entry->save(App::get_connection());
    }
    else { # increase the value of post 2
      if ($post_has_more_position_points) { # you can gain up to 3 points
        if ($post_rating_delta < 3) {
          $other_post_dimension_entry->position_points += 1;
        }
        elseif ($post_rating_delta < 6) {
          $other_post_dimension_entry->position_points += 2;
        }
        else {
          $other_post_dimension_entry->position_points += 3;
        }
      }
      else {
        $other_post_dimension_entry->position_points += 1;
      }
      $other_post_dimension_entry->save(App::get_connection());
    }
  }

}