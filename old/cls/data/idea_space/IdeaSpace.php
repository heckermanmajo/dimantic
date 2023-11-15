<?php

namespace cls\data\idea_space;

use App;
use cls\DataClass;

class IdeaSpace extends DataClass {
  var int $author_id = 0;
  var string $description = "";
  var string $created_date = "";
  var int $open = 1;

  var int $_number_of_members = 0;
  var int $_number_of_posts = 0;

  function __construct(array $data_from_db = []) {
    parent::__construct($data_from_db);

    $this->_number_of_members = self::get_count(
      App::get_connection(),
      "SELECT COUNT(*) FROM IdeaSpaceMembership WHERE idea_space_id = ?",
      [$this->id]
    );

    $this->_number_of_posts = self::get_count(
      App::get_connection(),
      "SELECT COUNT(*) FROM Post WHERE idea_space_id = ?",
      [$this->id]
    );

  }

  function get_title(): string {
    # todo use the interpreter to get the title
    return substr($this->description, 0, 40);
  }

  function put_display_card(): void {
    ?>
    <div class="w3-card-4 w3-margin w3-padding">
      <p>Members: <?=$this->_number_of_members?></p>
      <p>Posts: <?=$this->_number_of_posts?></p>
      <a style="text-decoration: none" href="/idea_space.php?id=<?= $this->id ?>">
        <h3><?= $this->get_title() ?></h3>
        <pre><?= json_encode($this, JSON_PRETTY_PRINT) ?></pre>
      </a>
    </div>
    <?php
  }

  /**
   * A post needs to win support to compete in a league season for any given
   * idea space. In order to make sure it stays fair, the
   * more members an ides space has, the more support a post
   * needs to win in order to compete in a league season.
   */
  function get_number_of_needed_support_for_post_to_compete_in_league(): int {

    # todo: problem: if a user just joins a space and never rates and a lot users
    #       do this,. they make it harder , if not impossible for this space to
    #       get posts into any league
    #       We can fix this by only counting active members of the space, that have rated
    #       recently and enough times

    return 0; # todo: make dynamic
  }
}