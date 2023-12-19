<?php

namespace cls\data\space;

use cls\App;
use cls\DataClass;
use cls\StringUtils;

class Space extends DataClass {
  /**
   * @var string Description of the space in markdown.
   */
  var string $content = "# Empty Space";
  var int $created_at = 0;
  var int $author_id = 0;

  /**
   *
   * @param App $app
   * @return array<Space>
   * @throws \Exception
   */
  static function getAllSpaces(App $app): array {
    $spaces = static::get_array(
      $app->get_database(),
      "SELECT * FROM space",
      [],
      Space::class
    );
    return $spaces;
  }

  static function getByContent(App $app, string $content): array {
    $spaces = static::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM space WHERE content = :content",
      params: ["content" => $content]
    );
    return $spaces;
  }

  function getDisplayCard(App $app): string {
    ob_start();
    
    $memberships = SpaceMembership::get_all_memberships_of_space(
      $app,
      $this->id,
    );
    
    ?>
    <div class="w3-card w3-margin w3-padding">

      <h3>
        <a style="text-decoration: none" href="/space.php?id=<?= $this->id ?>">
          <?= StringUtils::get_title_from_md_content($this->content) ?>
        </a>
        <div class="w3-right">
          <button> Wiki</button>
          <button> Marketplace</button>
          <button> Members</button>
        </div>
      </h3>

      <div class="w3-row">
        <div class="w3-third">
          <div class="w3-card w3-margin">
            <p>Most important</p>
          </div>
        </div>
        <div class="w3-third">
          <div class="w3-card w3-margin">
            <p>Most important</p>
          </div>
        </div>
        <div class="w3-third">
          <div class="w3-card w3-margin">
            <p>Most important</p>
          </div>
        </div>
      </div>

      <div class="w3-row">
        <div class="w3-third">
          <div class="w3-card w3-margin">
            <p>Most matching you</p>
          </div>
        </div>
        <div class="w3-third">
          <div class="w3-card w3-margin">
            <p>Most matching you</p>
          </div>
        </div>
        <div class="w3-third">
          <div class="w3-card w3-margin">
            <p>Most matching you</p>
          </div>
        </div>
      </div>

      <div class="w3-row">
        <div class="w3-third">
          <div class="w3-card w3-margin">
            <p>News xyz</p>
          </div>
        </div>
        <div class="w3-third">
          <div class="w3-card w3-margin">
            <p>News xyz</p>
          </div>
        </div>
        <div class="w3-third">
          <div class="w3-card w3-margin">
            <p>News xyz</p>
          </div>
        </div>
      </div>
      
      <?php
      
      foreach ($memberships as $membership) {
        echo $membership->get_card();
      }
      
      ?>
    </div>
    <?php
    return ob_get_clean();
  }
}