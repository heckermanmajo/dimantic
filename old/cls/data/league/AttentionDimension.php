<?php

namespace cls\data\league;

use cls\DataClass;

class AttentionDimension extends DataClass {

  use CreateAttentionDimensionRequest;

  var string $title = "";
  var string $description = "";
  var int $author_member_id = 0;
  var string $status = "";

  var int $_number_of_posts = 0;

  function put_edit_card() {
    # todo: implement correctly
    ?>
    <div class="w3-card w3-margin w3-padding">
      <h6><?= $this->title ?></h6>
      <pre><?= $this->description ?></pre>
      <p>Number of posts in category: <?= $this->_number_of_posts ?></p>
      TODO: edit-form
    </div>
    <?php
  }

  function echo_edit_card(): void {
    ?>
    <div class="w3-card w3-margin w3-padding">
      <h6><?= $this->title ?></h6>
      <pre><?= $this->description ?></pre>
      <p>Number of posts in category: <?= $this->_number_of_posts ?></p>
    </div>
    <?php
  }

}