<?php

namespace src\app\user\data\tables\account;

trait GetAccountByUsernameOrEmail {

  /**
   * @throws \Exception
   */
  static function get_by_username_or_email(
    string $username_or_email
  ): Account|null {

    $sql = "
      SELECT * FROM account
      WHERE username = :username
      OR email = :email
    ";

    $result = Account::get_one(
      $sql,
      [
        "username" => $username_or_email,
        "email" => $username_or_email
      ]
    );

    return $result;

  }

}