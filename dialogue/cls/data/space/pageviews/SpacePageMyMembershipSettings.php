<?php

namespace cls\data\space\pageviews;

use cls\App;
use cls\data\space\Space;

class SpacePageMyMembershipSettings {
  static function display(Space $space, App $app): string {
    ob_start();
    ?>
    <h3> My Membership-Settings </h3>
    <pre>
      Configure your relation to the space, when to get news, etc.
    </pre>
    <?php
    return ob_get_clean();
  }
}