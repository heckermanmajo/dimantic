<?php

namespace cls\data\conversation_blue_print;

use cls\App;
use cls\DataClass;

/**
 * Simple conversation rule for a conversation blueprint.
 * - since it is a blueprint, it is not a conversation yet.
 */
class ProtoRule extends DataClass {
  var int $author_id = 0;
  var int $blue_print_id = 0;
  var string $content = "";

  function get_card(App $app): string {
    ob_start();
    # todo: add numbers to the rules ...
    # todo: add edit button
    ?>
    <div class="w3-card w3-margin w3-padding">
      <h4> Proto Rule </h4>
      <pre>
        <?= $app->markdown_to_html($this->content) ?>
        <button>Edit Proto-Rule</button>
      </pre>
    </div>
    <?php
    return ob_get_clean();
  }
}