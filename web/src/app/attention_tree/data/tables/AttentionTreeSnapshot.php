<?php

namespace src\app\attention_tree\data\tables;

/**
 * A cron job that snapshots the attention tree every
 * 24 hours.
 *
 * can be put into another db at a certain point.
 */
class AttentionTreeSnapshot {
  var int $version;
  var int $user_id;
  var string $json_content;
  var string $created_at;
}