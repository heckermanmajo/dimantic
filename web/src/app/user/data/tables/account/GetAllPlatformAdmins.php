<?php

namespace src\app\user\data\tables\account;

use src\app\user\enums\AccountState;

trait GetAllPlatformAdmins {

  /**
   * @return array<Account>
   */
  static function getAllPlatformAdmins(): array {
    $admin_state = AccountState::PLATFORM_ADMIN->value;
    $table_name = self::getTableName();
    $sql = "SELECT * FROM account WHERE state = $admin_state";
  }

}