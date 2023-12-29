<?php

namespace cls\data\conversation_blue_print;

use cls\DataClass;

/**
 * Simple conversation rule for a conversation blueprint.
 * - since it is a blueprint, it is not a conversation yet.
 */
class ProtoRule extends DataClass {
  var int $blue_print_id = 0;
  var string $content = "";
}