<?php

namespace src\app\attention_profile\data\tables;

class AttentionSnippet {
  var int $attention_profile_id = 0;
  var string $content = "";
  var int $is_meta_description = 0;
  var int $wanted = 0; # -1 , 1
}