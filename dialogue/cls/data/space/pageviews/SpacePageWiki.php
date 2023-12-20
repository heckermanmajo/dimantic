<?php

namespace cls\data\space\pageviews;

use cls\App;
use cls\data\space\Space;

class SpacePageWiki {
  static function display(Space $space, App $app): string {
    ob_start();
    ?>
    <h3> Wiki </h3>
    <pre>
      Collection of documents.
    </pre>
    <?php
    return ob_get_clean();
  }
}