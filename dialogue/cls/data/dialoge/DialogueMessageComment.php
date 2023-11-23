<?php

namespace cls\data\dialoge;

use cls\App;
use cls\data\account\Account;
use cls\DataClass;

class DialogueMessageComment extends DataClass {
  var int $dialogue_message_id = 0;
  var int $account_id = 0;
  var string $selection = "";
  var string $comment_text = '';
  var string $create_date = '';

  function get_display_card(App $app): string {
    ob_start();
    $author = Account::get_by_id(
      $app->get_database(),
      $this->account_id,
    );
    if(trim($this->comment_text) == ""){
      return "";
    }
    ?>
    <div class="w3-card" style="font-size: 90%">
      <span style="margin-left: 2%"> >>"<b><?=$this->selection?></b>"</span>
      <pre style="padding-left: 5px; margin: 0"><?=$author->get_gravtar_profile_image(20)?><?=$author->name?>: <i><?=$this->comment_text?></i></pre>
    </div>
    <?php
    return ob_get_clean();
  }
}