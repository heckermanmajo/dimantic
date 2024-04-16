<?php

namespace src\app\user\requests\api;
use src\app\user\data\tables\account\Account;
use src\core\Component;
use src\core\Request;

class LoginRequest extends Request {

  function is_allowed(): bool {
    return true;
  }

  function is_valid(): bool {
    return true;
  }

  function execute(): Component|string|array|null {
    $a = new Account();
    $a->save();
    echo "Account created";
    return "/index.php";
  }


}