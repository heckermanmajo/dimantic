<?php

namespace cls\data\space\pageviews;

use cls\App;
use cls\data\space\Space;

class SpacePageNewSubspace {
  static function display(Space $space, App $app): string {
    ob_start();
    ?>
    <h3> Create a new Subspace</h3>

    <div class="info-card">

      ⚠️ Not implemented yet.

    </div>
    <?php
    return ob_get_clean();
  }
}