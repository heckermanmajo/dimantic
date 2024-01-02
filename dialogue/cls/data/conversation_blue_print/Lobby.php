<?php

namespace cls\data\conversation_blue_print;

use cls\DataClass;

/**
 * If you join a blueprint, you are added to a lobby.
 */
class Lobby extends DataClass {
  var int $author_id = 0;
  var int $conversation_blueprint_id = 0;
}