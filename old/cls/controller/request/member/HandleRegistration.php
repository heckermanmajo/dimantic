<?php

namespace cls\controller\request\member;

use App;
use cls\data\account\Account;
use cls\RequestError;

class HandleRegistration {
  static function execute(): Account|RequestError {

    $account = new Account();
    $account->name = $_POST["name"];
    $account->email = $_POST["email"];
    $account->password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $account->save(App::get_connection());
    App::login($account);

    return $account;
  }
}