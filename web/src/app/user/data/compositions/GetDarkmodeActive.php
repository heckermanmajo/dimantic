<?php

namespace src\app\user\data\compositions;

use src\core\Composition;

final class GetDarkmodeActive extends Composition {
  static function is_active(): bool {
    # todo: use time or user preferences to determine if darkmode is active
    return false;
  }
}