<?php

namespace cls\data\post;

use App;
use cls\data\idea_space\IdeaSpace;
use cls\data\league\AttentionLeague;
use cls\data\league\AttentionLeagueSeason;
use cls\data\post\request\CreateAnswerPost;
use cls\DataClass;
use cls\Interpreter;

class Post extends DataClass {

  const NEW_POST_TEXT = "New Post - replace with your awesome content";

  use PostViews;
  use PostQueries;

  # request
  use CreateAnswerPost;

  var string $content = "";
  var int $author_id = 0;
  var int $parent_post_id = 0;
  var string $command_error_log = "";
  var string $command_feedback_log = "";
  var string $created_at = "";
  var int $is_thread_post = 0;
  /**
   * If this post has won the qualifying, it cannot be edited anymore.
   * Also, it cannot be used to compete in a league again.
   * It is basically a "final" post.
   */
  var int $has_won_qualifying = 0;
  var int $published = 0;
  /**
   * Every post is always part of an idea space.
   * If there is a post-competition entry, the post also
   * competes in the selected league - IF it wins out
   * within the idea space ratings.
   */
  var int $idea_space_id = 0;

  /**
   * If a post wants to compete in a liga, this field is set to
   * the season id. If this is 0 -  the post is just a community post.
   * Should only be set if the post is not an answer to another post.
   *
   * Can only be edited if not published yet.
   */
  var int $liga_season_id = 0;

  /**
   * "Collective correction notes"
   * If this post is a correction, it i basically a community note
   * to correct the post it answers to.
   * It behaves differently than normal answers.
   */
  var int $this_is_correction = 0;

  /**
   * This is an absolute number that is used to sort posts and determine the
   * probability for automatic attention boosts.
   */
  var int $virtue = 0;
  # how many attention does this post get? -> personal importance for members based on appearance in attention pathe

  var string $_author_name = "[no author name available]";
  var int $_number_of_direct_children = 0;
  var int $_number_of_members = 0;
  var int $_is_observed = 0;
  var int $_post_support = 0;


  var ?AttentionLeague $__league = null;
  var ?AttentionLeagueSeason $__season = null;
  var ?IdeaSpace $__idea_space = null;

  public function get_league(): ?AttentionLeague {
    if ($this->__league === null) {
      $this->__league = AttentionLeague::get_one(
        App::get_connection(),
        "SELECT * FROM `AttentionLeague` WHERE `id` =
        (SELECT AttentionLeagueSeason.attention_league_id FROM AttentionLeagueSeason WHERE AttentionLeagueSeason.id = :season_id);",
        ["season_id" => $this->liga_season_id]
      );
    }

    #if ($this->__league === null) {
    #  throw new \Exception("No league found for post");
    #}

    return $this->__league;
  }

  function get_season(): ?AttentionLeagueSeason {
    if ($this->__season === null) {
      $this->__season = AttentionLeagueSeason::get_one(
        App::get_connection(),
        "SELECT * FROM `AttentionLeagueSeason` WHERE `id` = :season_id;",
        ["season_id" => $this->liga_season_id]
      );
    }

    #if ($this->__season === null) {
    #  throw new \Exception("No season found for post");
    #}

    return $this->__season;
  }

  function get_idea_space_i_belong_to(): IdeaSpace {
    if ($this->__idea_space === null) {
      $this->__idea_space = IdeaSpace::get_one(
        App::get_connection(),
        "SELECT * FROM `IdeaSpace` WHERE `id` = ?;",
        [$this->idea_space_id]
      );
    }

    if ($this->__idea_space === null) {
      throw new \Exception("No idea space found for post");
    }

    return $this->__idea_space;
  }

  public function __construct(array $data_from_db = []) {
    $this->created_at = date("Y-m-d H:i:s");
    parent::__construct($data_from_db);
    if ($this->created_at === "") {
      $this->created_at = date("Y-m-d H:i:s");
    }
    if ($this->id > 0) {
      assert($this->author_id > 0, "author_id must be set higher than 0" . $this->content);
      if ($this->parent_post_id != 0) {
        assert($this->idea_space_id == 0);
        assert($this->liga_season_id == 0);
      }
      if ($this->this_is_correction == 1) {
        assert($this->idea_space_id == 0);
        assert($this->liga_season_id == 0);
        assert($this->parent_post_id != 0);
      }
    }
  }

  /**
   * @return array<int, Post>
   */
  function get_post_parent_history(): array {
    $history = [];
    $parent_id = $this->parent_post_id;
    while ($parent_id > 0) {
      $parent = Post::get_one(
        App::get_connection(),
        "SELECT * FROM `Post` WHERE `id` = ?;",
        [$parent_id]
      );
      $history[] = $parent;
      $parent_id = $parent->parent_post_id;
    }
    return $history;
  }

  function get_title(int $max_title_length = 40): string {
    # todo: now we possibly cut into html ....
    $content = Interpreter::execute_always_commands($this);
    #echo htmlspecialchars($content);
    foreach (explode("<br>", $content) as $line) {
      if (trim(strip_tags($line)) === "") {
        continue;
      }
      $title = strip_tags($line);
      if ($max_title_length < 0) {
        return $title;
      }
      return substr($title, 0, $max_title_length);
    }
    return "[no title]";
  }

  function get_short_desc(): string {
    $content = Interpreter::execute_always_commands($this);
    #echo htmlspecialchars($content);
    $short_desc = [];
    $ignore_title_flag = true;
    foreach (explode("<br>", $content) as $line) {
      if (trim(strip_tags($line)) === "") {
        continue;
      }
      if ($ignore_title_flag) {
        $ignore_title_flag = false;
        continue;
      }
      $short_desc[] = $line;
      if (count($short_desc) > 3) {
        break;
      }
    }
    if (count($short_desc) == 0) {
      return "[no content]";
    }
    return implode("<br>", $short_desc);
  }


}