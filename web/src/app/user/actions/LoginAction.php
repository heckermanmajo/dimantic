<?php

namespace src\app\user\actions;
use src\app\user\data\tables\account\Account;
use src\core\Action;

class LoginAction extends Action {

  function __construct(
    private Account $account
  ) {}

  function is_allowed(): bool {
    return true;
  }

  function execute(): void {

    $_SESSION["user"] = $this->account;

  }

}