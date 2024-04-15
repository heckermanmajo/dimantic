<?php

namespace src\global\compositions;

final class GetCurrentlyLoggedInAccount {

  static function get_account(): ?Account {
    return $_SESSION['user'] ?? null;
  }

  static function somebody_is_logged_in(): bool {
    return self::get_account() !== null;
  }

}