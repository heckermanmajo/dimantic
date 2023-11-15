<?php

namespace cls\data\post;

use cls\DataClass;

class PostSupportEntry extends DataClass {
  var int $post_id = 0;
  var int $account_id = 0;
  var int $season_id = 0;
  var string $create_date = "";
}