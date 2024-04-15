<?php

namespace src\global\compositions;

use src\core\Composition;

final class GetDevice extends Composition {
  static function is_mobile(): bool{
    return false;
  }
}