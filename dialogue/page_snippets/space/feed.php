<?php


use cls\App;
use cls\data\space\Space;

return function (Space $space, App $app): string {
  ob_start();
  ?>
  <p>
    The Feed of this space: the most important stuff in kacheln...
  </p>
  <?php
  return ob_get_clean();
};