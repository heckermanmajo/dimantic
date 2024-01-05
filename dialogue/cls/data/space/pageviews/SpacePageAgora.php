<?php

namespace cls\data\space\pageviews;

use cls\App;
use cls\data\space\Space;

/**
 * Class SpacePageAgora
 *
 * The SpacePageAgora class is responsible for displaying the Agora page.
 *
 * An Agora is a term from ancient Greek which refers to a public open space used for assemblies and markets.
 */
class SpacePageAgora {
  static function display(Space $space, App $app): string {
    ob_start();
    ?>
    <h3> Agora </h3>


    <form method="post">

    </form>

    <?php
    return ob_get_clean();
  }
}