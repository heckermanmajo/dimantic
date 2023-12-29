<?php

namespace cls\data\space\pageviews;

use cls\App;
use cls\data\space\Space;

class SpacePageBlueprints {
  static function display(Space $space, App $app): string {
    ob_start();
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
    return ob_get_clean();
  }
}