<?php

namespace cls\controller\request\member;

use App;
use cls\data\account\Account;
use cls\RequestError;

class HandleLogin {
  static function execute(): Account|RequestError {

    try {

      if (!isset($_POST["email_or_name"])) {
        return new RequestError("email_or_name is not set in post data", RequestError::BAD_REQUEST);
      }

      if (!isset($_POST["password"])) {
        return new RequestError("password is not set in post data", RequestError::BAD_REQUEST);
      }

      $email_or_name = $_POST["email_or_name"];
      $password = $_POST["password"];

      $account = Account::get_one(
        App::get_connection(),
        "SELECT * FROM `Account` WHERE `email` = ? OR `name` = ?;",
        [$email_or_name, $email_or_name]
      );

      if ($account == null) {
        return new RequestError(
          "No account with this email or name exists.",
          RequestError::USER_INPUT_ERROR
        );
      }
      else {
        if (password_verify($password, $account->password)) {
          App::login($account);
        }
        else {
          return new RequestError(
            "Wrong Email or Password.",
            RequestError::USER_INPUT_ERROR
          );
        }
      }

    }

    catch (\PDOException $t) {
      return new RequestError(
        "Error while logging in: PDOException.",
        RequestError::SYSTEM_ERROR,
        e: $t
      );
    }

    catch (\Throwable $t) {
      return new RequestError(
        "Error while logging in.",
        RequestError::SYSTEM_ERROR,
        e: $t
      );
    }

    return $account;

  }
}