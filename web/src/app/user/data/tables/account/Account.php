<?php

namespace src\app\user\data\tables\account;
use src\app\user\enums\AccountState;
use src\core\table\Table;

final class Account extends Table {

  use GetAllPlatformAdmins;

  public AccountState $state = AccountState::FROZEN;

}