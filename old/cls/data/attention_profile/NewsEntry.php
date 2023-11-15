<?php

namespace cls\data\attention_profile;

use cls\DataClass;

class NewsEntry extends DataClass {
  # todo: make this class use attention-profile
  var string $title = "";
  var string $content = "";
  var int $target_member_id = 0;
  var int $post_id = 0;
  var string $news_type = "";
  var string $status = "new"; # seen, archived
  var string $attention_source = ""; # observation, membership, ownership, systemnews, task
  var int $answer_post_id = 0;

  function get_news_entry_view_as_string(): string {
    ob_start();
    # todo: make thois form reach to an endpoint
    ?>
    <div class="w3-card-4 w3-margin w3-padding">
      <div><b><?=$this->attention_source?></b></div>
      <h3><?= $this->title ?></h3>
      <div><?= $this->content ?></div>
      <form method="post" style="display: inline">
        <input type="hidden" value="<?=$this->id?>" name="news_entry_id">
        <input type="hidden" value="delete_news_entry" name="action">
        <button class="delete-button">Delete</button>
      </form>
      <pre>
        <?= json_encode($this, JSON_PRETTY_PRINT) ?>
      </pre>
    </div>
    <?php
    return ob_get_clean();
  }
}