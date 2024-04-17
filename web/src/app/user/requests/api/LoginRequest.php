<?php

namespace src\app\user\requests\api;

use src\app\user\actions\LoginAction;
use src\app\user\data\tables\account\Account;
use src\core\Component;
use src\core\exceptions\BadValue;
use src\core\exceptions\DataNotFound;
use src\core\exceptions\FieldNotFound;
use src\core\Request;

final class LoginRequest extends Request {

  private Account $account;

  function is_allowed(): bool {
    if ($this->user !== null) {
      $this->why_not_allowed = "Already logged in, but tried login";
      return false;
    }
    return true;
  }

  /**
   * @throws FieldNotFound
   * @throws \Exception
   */
  function _is_valid(): void {

    $username = $this->post['username'] ?? throw new FieldNotFound("Username is required");

    $password = $this->post['password'] ?? throw new FieldNotFound("Password is required");

    $account = Account::get_by_username_or_email($username);

    if ($account === null) {
      throw new DataNotFound("Account not found");
    }

    if (!password_verify($password, $account->password)) {
      throw new BadValue("Password is incorrect");
    }

    $this->account = $account;

  }

  function execute(): Component|string|array|null {
    $la = new LoginAction($this->account);
    $la->execute();
    return "/index.php?p=home";
  }


}