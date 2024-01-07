<?php

use cls\App;
use cls\data\space\Space;

return function (Space $space, App $app): string {
  ob_start();
  ?>
  <h3> My Conversations </h3>
  <label>
    <input type="radio" name="state" value="ongoing">
    ongoing
  </label>
  <label>
    <input type="radio" name="state" value="done">
    done
  </label>
  <?php
  return ob_get_clean();
};