<?php

namespace cls\data\space\pageviews;

use cls\App;
use cls\data\space\Space;

class SpacePageCreate {
  static function display(Space $space, App $app): string {
    ob_start();
    ?>
    <div class="w3-row">
      <a class="tab-button sketch-card w3-third w3-center"
         href="/space.php?p=new_subspace&id=<?= $_GET["id"] ?>">
        <div style="
          border-bottom: solid 1px black;
        height: 300px;display: flex;justify-content: center;align-items: center;">
          <img src="/res/create_subspace.svg">
        </div>
        Create Conversation Blueprint
      </a>
      <a class="tab-button sketch-card w3-third w3-center"
         href="/space.php?p=new_blueprint&id=<?= $_GET["id"] ?>">
        <div style="
          border-bottom: solid 1px black;
        height: 300px;display: flex;justify-content: center;align-items: center;">
          <img src="/res/blueprint.svg">
        </div>
        Create Conversation Blueprint
      </a>
      <a class="tab-button sketch-card w3-third w3-center"
         href="/space.php?p=new_document&id=<?= $_GET["id"] ?>">
        <div style="
          border-bottom: solid 1px black;
        height: 300px;display: flex;justify-content: center;align-items: center;">
          <img src="/res/document.svg">
        </div>
        Create Document
      </a>
    </div>
    <?php
    return ob_get_clean();
  }
}