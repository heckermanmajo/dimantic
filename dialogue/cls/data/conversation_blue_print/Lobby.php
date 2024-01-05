<?php

namespace cls\data\conversation_blue_print;

use cls\App;
use cls\DataClass;

/**
 * If you join a blueprint, you are added to a lobby.
 */
class Lobby extends DataClass {
  var int $author_id = 0;
  var int $conversation_blueprint_id = 0;

  /**
   * @throws \Exception
   */
  static function get_lobbies_of_conversation_blueprint(
    App $app,
    int $conversation_blueprint_id,
  ): array {
    return self::get_array(
      $app->get_database(),
      "SELECT * FROM Lobby WHERE conversation_blueprint_id = ?",
      [$conversation_blueprint_id]
    );
  }

  function display_card(App $app): string {
    ob_start();
    ?>
    <div class="w3-card-4 w3-padding">
      <h4> Lobby </h4>
      <pre><?=json_encode($this, JSON_PRETTY_PRINT)?></pre>

      <button>
        JOIN LOBBY (todo: implement)
      </button>

      <pre>List of members in lobby </pre>

    </div>
    <?php
    return ob_get_clean();
  }
}