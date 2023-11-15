<?php

namespace cls\data\account;


use App;
use cls\DataClass;

class Account extends DataClass {
  var string $name = "";
  var string $email = "";
  var string $password = "";
  var string $time_zone = "Europe/Berlin";
  var string $language = "de";

  # static: set by superroot
  var string $static_rank = "member";
  # dynamic: based on value delivery within the network (trust & competencey score)
  var string $dynamic_rank = "member";

  var string $create_date = "";

  var int $trust_score = 0;
  var int $online_score = 0;

  function put_display_card(): void {
    ?>
    <div class="w3-card-4 w3-margin w3-padding">
      <a style="text-decoration: none" href="/profile.php?id=<?= $this->id ?>">
        <h3>
          <img class="w3-round-xxlarge" src="<?=App::get_gravatar(
            $this->email,
            80,
            "wavatar"
          )?>" alt="Profile image of <?=$this->name?>" />
          <?= $this->name ?></h3>
        <pre><?= json_encode($this, JSON_PRETTY_PRINT) ?></pre>
      </a>
    </div>
    <?php
  }
}
