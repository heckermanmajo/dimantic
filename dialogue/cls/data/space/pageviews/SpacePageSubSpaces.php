<?php

namespace cls\data\space\pageviews;

use cls\App;
use cls\data\space\Space;

/**
 * A tree of all sub spaces of the current space.
 * -> JS Tree.
 */
class SpacePageSubSpaces {
  static function display(Space $space, App $app): string {
    ob_start();
    ?>
    <h3> Subspaces as JS TREE </h3>
    <pre>
      The admins and members can create, close, delete subspaces...
    </pre>
    <?php
    return ob_get_clean();
  }
}