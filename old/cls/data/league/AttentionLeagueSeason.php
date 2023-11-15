<?php

namespace cls\data\league;

use App;
use cls\data\post\Post;
use cls\DataClass;

class AttentionLeagueSeason extends DataClass {
  var int $attention_league_id = 0;
  var string $season_description = "";
  var string $season_start_date = "";
  var string $state = "open";  # rating, closed

  var ?AttentionLeague $__league = null;

  function get_attention_league(): AttentionLeague {
    if ($this->__league === null) {
      $this->__league = AttentionLeague::get_one(
        App::get_connection(),
        "SELECT * FROM `AttentionLeague` WHERE `id` = ?;",
        [$this->attention_league_id]
      );
    }

    if ($this->__league === null) {
      throw new \Exception("No league found for season");
    }

    return $this->__league;
  }

  function get_days_needed_to_start(): float {
    $league = $this->get_attention_league();
    $days_per_season = $league->days_per_season;
    $season_start_date = $this->season_start_date;
    $now = date("Y-m-d H:i:s");
    $diff = strtotime($now) - strtotime($season_start_date);
    $days = $diff / (60 * 60 * 24);
    $days_needed_to_start = $days_per_season - $days;
    if ($days_needed_to_start < 0) {
      $days_needed_to_start = 0;
    }
    return $days_needed_to_start;
  }

  function get_number_of_posts_of_season(): int {
    return self::get_count(
      App::get_connection(),
      "SELECT COUNT(*) FROM Post WHERE liga_season_id = ? AND published = 1;",
      [$this->id]
    );
  }

  /**
   * @return array<Post>
   * @throws \Exception
   */
  function get_qualified_posts(): array {
    $all_posts_for_this_season = Post::get_array(
      App::get_connection(),
      "SELECT *, 
       (SELECT COUNT(*) FROM PostSupportEntry WHERE PostSupportEntry.post_id = Post.id) AS _post_support 
        FROM Post WHERE liga_season_id = ? AND published = 1;",
      [$this->id]
    );

    $posts_mapped_on_idea_space = [];
    foreach ($all_posts_for_this_season as $post) {
      if ($post->_post_support != 0) {
        App::$logs[] = "Found post with support Post: " . $post->_post_support;
      }
      $idea_space_id = $post->idea_space_id;
      if (!isset($posts_mapped_on_idea_space[$idea_space_id])) {
        $posts_mapped_on_idea_space[$idea_space_id] = [];
      }
      $posts_mapped_on_idea_space[$idea_space_id][] = $post;
    }

    # sort by support
    foreach ($posts_mapped_on_idea_space as &$all_posts_per_idea_space) {
      usort($all_posts_per_idea_space, function (Post $a, Post $b) {
        return $b->_post_support <=> $a->_post_support;
      });
      App::$logs[] = "First post: " . $all_posts_per_idea_space[0]->_post_support;
    }

    $qualified_posts_per_idea_space = [];
    foreach ($posts_mapped_on_idea_space as &$all_posts_per_idea_space) {

      # only take the max number of posts per idea space
      $possibly_qualified_posts = array_slice(
        array: $all_posts_per_idea_space,
        offset: 0,
        length: $this->get_attention_league()->max_number_of_posts_per_season_per_idea_space
      );

      # only count as qualified if the post has enough support
      # -> the needed support is determined by the idea space itself
      #    since it is based on the number of members of given space
      $qualified_posts = [];
      /** @var Post $post */
      foreach ($possibly_qualified_posts as $post) {
        if ($post->_post_support >= $post->get_idea_space_i_belong_to()->get_number_of_needed_support_for_post_to_compete_in_league()) {
          $qualified_posts[] = $post;
        }
      }

      $qualified_posts_per_idea_space[] = $qualified_posts;
    }

    $all_qualified_posts = [];
    foreach ($qualified_posts_per_idea_space as $qualified_posts) {
      $all_qualified_posts = array_merge($all_qualified_posts, $qualified_posts);
    }

    return $all_qualified_posts;
  }

  /**
   * Returns the number of qualified posts.
   *
   * @return int
   * @throws \Exception
   */
  function get_number_of_currently_qualified_posts(): int {

    $all_qualified_posts = $this->get_qualified_posts();

    return count($all_qualified_posts);

  }

  function can_change_state_from_open_to_rating(): bool {
    if($this->state != "open"){
      return false;
    }
    return
      $this->get_days_needed_to_start() == 0
      &&
      (
        $this->get_number_of_currently_qualified_posts()
        >= $this->get_attention_league()->number_of_minimum_posts_per_season
      );
  }

  function can_change_state_from_rating_to_closed(): bool {
    # todo: implement
    return true;
  }

  function echo_simple_card(): void {
    $league = $this->get_attention_league();
    $is_open = $this->state == "open";
    ?>
    <div class="w3-card-4 w3-margin w3-padding">
      <h3><?= $this->season_description ?></h3>
      <p>Start-Date: <?= $this->season_start_date ?></p>
      <?php
      if ($is_open) {

        if ($this->get_days_needed_to_start() == 0) {
          ?>
          Enough time has passed to start the season.
          <?php
        }
        else {
          ?>
          Not enough time has passed to start the season.<br>
          <?php
          echo "Days needed to start: " . $this->get_days_needed_to_start() . "<br>";
        }

        $number_of_qualified_posts = $this->get_number_of_currently_qualified_posts();

        if ($number_of_qualified_posts < $league->number_of_minimum_posts_per_season) {
          ?>
          Not enough posts to start the season. <br>
          <?php
          echo "Number of all posts: " . $this->get_number_of_posts_of_season() . "<br>";
          echo "Number of qualified posts: " . $number_of_qualified_posts . "<br>";
          echo "Number of posts needed: " . $league->number_of_minimum_posts_per_season . "<br>";
        }
        else {
          ?>
          <p> Number of all posts: <?= $this->get_number_of_posts_of_season() ?></p>
          <p>Enough posts in season to start the season: <?= $number_of_qualified_posts ?> </p>
          <?php
        }
      }
      ?>

      State: <b><?= $this->state ?></b>
    </div>
    <?php
  }
}