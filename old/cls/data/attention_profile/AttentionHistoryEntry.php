<?php

namespace cls\data\attention_profile;

use cls\DataClass;

class AttentionHistoryEntry extends DataClass {
  var int $post_id = 0;
  var int $attention_path_id = 0;
  var string $create_date = "";
  var int $counter = 0;
}