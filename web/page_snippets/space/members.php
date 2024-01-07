<?php

use cls\App;
use cls\data\space\Space;

return function (Space $space, App $app): string {
  ob_start();
  ?>
  <h3> List of all members </h3>
  <pre>
      List of all members of this space.
    </pre>
  <?php
  return ob_get_clean();
};
