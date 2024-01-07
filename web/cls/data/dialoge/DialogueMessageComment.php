<?php

namespace cls\data\dialoge;

use cls\App;
use cls\data\account\Account;
use cls\DataClass;
use cls\lib\Parsedown;

/**
 * A comment for a message in a dialogue.
 * todo: should also cost some stuff...
 */
class DialogueMessageComment extends DataClass {
  var int $dialogue_message_id = 0;
  var int $account_id = 0;
  var string $selection = "";
  var string $comment_text = '';
  var string $create_date = '';

  function get_display_card(int $message_number = 0, int $comment_number = 0): string {
    ob_start();
    $author = Account::get_by_id(
      App::get()->get_database(),
      $this->account_id,
    );
    if (trim($this->comment_text) == "") {
      return "";
    }
    ?>
    <div class="w3-card" style="font-size: 90%; border-radius: 10px 10px;margin-bottom: 3px;">
      <div style="padding-left: 5px; padding-right: 5px; margin: 0">
        <div>
          <b style="font-size: 80%; color: dodgerblue; font-style: italic"> §<?= $message_number ?>#<?= $comment_number ?></b>
          <div class="w3-right">
          <span style="font-size: 80%; color: #818181">
            <?= $this->create_date ?>
          </span>
            <?= $author->get_gravtar_profile_image(20) ?>
          </div>
        </div>
        <span>"<i style="color:darkblue"><?= $this->selection ?></i>"</span><br>
        <?php
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);
        echo $parsedown->text($this->comment_text);
        ?>
      </div>
    </div>
    <?php
    return ob_get_clean();
  }
}