<?php

namespace cls\data\space\pageviews;

use cls\App;
use cls\data\space\Space;

class SpacePageFilter {
  static function display(Space $space, App $app): string {
    ob_start();
    ?>
    <h3> Filter </h3>
    <pre>
      Here you can filter all stuff
    </pre>
    <?php
    return ob_get_clean();
  }
}