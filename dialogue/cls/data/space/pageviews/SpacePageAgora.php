<?php

namespace cls\data\space\pageviews;

use cls\App;
use cls\data\space\Space;

class SpacePageAgora {
  static function display(Space $space, App $app): string {
    ob_start();
    ?>
    <h3> Agora </h3>
    <?php
    return ob_get_clean();
  }
}