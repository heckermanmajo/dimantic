<?php

namespace cls\data\space;

use cls\App;
use cls\DataClass;
use cls\StringUtils;

/**
 * A Space is a communication-context.
 *
 * It can have
 * - members
 * - conversations
 * - documents
 *
 * They can be referenced like this:
 * - www.dimantic.com?space=1&conversation=1
 * - www.dimantic.com?space=1&member=1
 * - www.dimantic.com?space=1&document=2
 *
 */
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
    return static::get_array(
      $app->get_database(),
      "SELECT * FROM space",
      [],
      Space::class
    );
  }

  /**
   * @throws \Exception
   */
  static function getByContent(App $app, string $content): array {
    return static::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM space WHERE content = :content",
      params: ["content" => $content]
    );
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
        <!--<div class="w3-right">
          <button> Wiki </button>
          <button> Agora </button>
          <button> Members</button>
        </div>-->
      </h3>

      <div class="w3-row">
        <div class="w3-third">
          <div class="w3-card w3-margin">
            <p>Most important by authority</p>
          </div>
        </div>
        <div class="w3-third">
          <div class="w3-card w3-margin">
            <p>Most important</p>
          </div>
        </div>
        <div class="w3-third">
          <div class="w3-card w3-margin">
            <p>Most important by authority</p>
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