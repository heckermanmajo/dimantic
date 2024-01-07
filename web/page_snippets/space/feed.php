<?php


use cls\App;
use cls\data\conversation_blue_print\ConversationBluePrint;
use cls\data\space\Space;

return function (Space $space, App $app): string {
  ob_start();

  $blueprints = ConversationBluePrint::get_all_published_blueprints(
    $space->id
  );
  #var_dump($blueprints);
  foreach ($blueprints as $blueprint){
    echo $blueprint->get_display_card($app);
  }

  ?>
  <p>
    The Feed of this space: the most important stuff in kacheln...
  </p>
  <?php
  return ob_get_clean();
};