<?php
declare(strict_types=1);

use cls\App;
use cls\data\account\Account;
use cls\Protocol;
use cls\RequestError;

if (count(debug_backtrace()) == 0) {
  require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}

function register(
  array $post_data,
): Account|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  
  $app = App::get();
  
  if (!isset($post_data["username"])) {
    return new RequestError(
      dev_message: "\$post_data[\"username\"] not set",
      code: RequestError::BAD_REQUEST,
    );
  }

  if (!isset($post_data["password"])) {
    return new RequestError(
      dev_message: "\$post_data[\"password\"] not set",
      code: RequestError::BAD_REQUEST,
    );
  }

  if (!isset($post_data["email"])) {
    return new RequestError(
      dev_message: "\$post_data[\"email\"] not set",
      code: RequestError::BAD_REQUEST,
    );
  }

  $username = $post_data["username"];
  $password = $post_data["password"];
  $email = $post_data["email"];

  $error_or_null = Account::check_value(
    field_name: "name",
    value: $username
  );
  if ($error_or_null !== null) {
    return new RequestError(
      dev_message: $error_or_null,
      code: RequestError::USER_INPUT_ERROR,
    );
  }

  $error_or_null = Account::check_value(
    field_name: "password",
    value: $password
  );
  if ($error_or_null !== null) {
    return new RequestError(
      dev_message: $error_or_null,
      code: RequestError::USER_INPUT_ERROR,
    );
  }

  $error_or_null = Account::check_value(
    field_name: "email",
    value: $email
  );
  if ($error_or_null !== null) {
    return new RequestError(
      dev_message: $error_or_null,
      code: RequestError::USER_INPUT_ERROR,
    );
  }

  $account = new Account();
  $account->name = $username;
  $account->password = password_hash($password, PASSWORD_DEFAULT);
  $account->email = $email;
  $account->create_date = date("Y-m-d H:i:s");

  $account->save($app->get_database());

  $app->login($account);

  return $account;
}

return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: register(...)
);