<?php

namespace cls\data\space\pageviews;

use cls\App;
use cls\data\conversation_blue_print\ConversationBluePrint;
use cls\data\space\Space;

class SpacePageBlueprints {
  /**
   * @throws \Exception
   */
  static function display(Space $space, App $app): string {
    ob_start();

    $blueprints = ConversationBluePrint::get_array(
      $app->get_database(),
      "SELECT * FROM ConversationBluePrint WHERE space_id = ? AND author_id = ?",
      [$space->id, $app->get_currently_logged_in_account()->id]
    );
    ?>
    <h3> Blue-Print management </h3>
    <pre>

      You can clone a blueprint any time-

      But once a conversation exists, you cannot edit the blueprint anymore.
      -> so if you wish to edit a "in use" blueprint, you can
      clone it and edit the clone.

    </pre>

    <button> Currently Published </button>
    <button> Unpublished </button>

    <?php
    foreach ($blueprints as $blueprint) {
      echo $blueprint->get_card($app);
    }
    ?>

    <?php
    return ob_get_clean();
  }
}