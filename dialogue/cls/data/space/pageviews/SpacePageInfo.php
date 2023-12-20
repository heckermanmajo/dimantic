<?php

namespace cls\data\space\pageviews;

use cls\App;
use cls\data\space\Space;class SpacePageInfo {
    static function display(Space $space,App $app): string {
    ob_start();
    ?>
      <p>
        <?= $app->markdown_to_html($space->content) ?>
      </p>
    <?php
    return ob_get_clean();
  }

}