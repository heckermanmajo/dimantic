<?php

namespace src\global\compositions;

use src\app\user\data\tables\account\Account;
use src\core\exceptions\NotLoggedIn;

final class GetCurrentlyLoggedInAccount {

  static function get_account(): ?Account {
    return $_SESSION['user'] ?? null;
  }

  /**
   * @throws NotLoggedIn
   */
  static function get_account_or_throw(): Account {
    return $_SESSION['user'] ?? throw new NotLoggedIn("User is not logged in");
  }

  static function somebody_is_logged_in(): bool {
    return self::get_account() !== null;
  }

}