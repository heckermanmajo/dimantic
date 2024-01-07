<?php

use cls\App;
use cls\data\space\Space;

return function (Space $space, App $app): string {
  ob_start();
  ?>
  <p>
    <?= $app->markdown_to_html($space->content) ?>
  </p>
  <?php
  return ob_get_clean();
};

