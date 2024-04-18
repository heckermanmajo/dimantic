<?php

namespace src\app\user\data\tables\account;

use src\app\user\data\enums\AccountState;
use src\core\table\Table;

final class Account extends Table {

  use GetAllPlatformAdmins;
  use GetAccountByUsernameOrEmail;

  function __construct(
    public string $username = "",
    public string $email = "",
    public string $password = "",
    public AccountState $state = AccountState::NEW_USER,
    array  $data_from_db = []
  ) {
    parent::__construct($data_from_db);
  }

}