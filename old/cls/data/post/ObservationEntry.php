<?php

namespace cls\data\post;

use cls\DataClass;

class ObservationEntry extends DataClass {
  var int $post_id = 0;
  var int $tree_id = 0;
  var int $attention_path_id = 0;
  var string $create_date = "";
}