<?php

namespace cls\data\attention_profile;

use App;
use cls\DataClass;

class AttentionProfile extends DataClass {

  var string $title = "";
  var string $description = "";
  var int $owner_member_id = 0;
  var string $created_at = "";

  /**
   * List of ids comma seperated of trees or posts, first trees, then ";" then posts.
   * That are displayed at the feed.
   *
   * Feed ony has finite number (e.g. 6) of posts and is updated only every day.
   * This fights against addiction, but ensures retention.
   */
  var string $feed_ids = "";

  function get_manage_card(bool $with_select_form = true): string {
    ob_start();
    ?>
    <div class="w3-card-4 w3-margin w3-padding">
      <input
        style="font-size: 150%"
        class="w3-margin"
        value="<?= $this->title ?>"
        oninput="$.post('ajax/update_attention_profile.php', {
          id: <?= $this->id ?>,
          title: this.value
          }).done(function (data) {
            console.log(data);
          })
          "
      >
      <br>
      <textarea
        style="width: 100%"
        class="w3-margin"
        oninput="
          $.post('ajax/update_attention_profile.php', {
          id: <?= $this->id ?>,
          description: this.value
          }).done(function (data) {
            console.log(data);
          })
          "
      ><?= $this->description ?></textarea>

      <br>

      <pre><?= json_encode($this, JSON_PRETTY_PRINT) ?></pre>
      <?php if ($with_select_form): ?>
        <?php if (App::$attention_profile->id == $this->id): ?>
          <span> This Attention path is selected </span>
        <?php else: ?>

          <form method="post">
            <input type="hidden" name="action" value="select_other_attention_profile">
            <input type="hidden" name="id" value="<?= $this->id ?>">
            <button class="button"> Select this attention Profile</button>
          </form>
        <?php endif; ?>
      <?php endif; ?>

    </div>
    <?php
    return ob_get_clean();
  }
}