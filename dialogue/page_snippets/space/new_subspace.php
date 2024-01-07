<?php

use cls\App;
use cls\data\space\Space;

return function (Space $space, App $app): string {
  ob_start();
  ?>
  <h3> Create a new Subspace</h3>

  <div class="info-card">

    ⚠️ Not implemented yet.

  </div>
  <?php
  return ob_get_clean();
};