<?php

namespace src\app\attention_tree\data\tables;

use src\app\attention_tree\data\enums\NodeType;
use src\core\table\Table;

/**
 * Data for an attention node.
 *
 * Always up to date.
 *
 */
class AttentionNode extends Table {
  var int $by_system_id;
  var NodeType $node_type;
  var int $reference_id;
  var string $note;
}