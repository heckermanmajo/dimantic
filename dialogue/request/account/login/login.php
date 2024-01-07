<?php
declare(strict_types=1);

use cls\App;
use cls\data\account\Account;
use cls\data\dialoge\Dialogue;
use cls\Protocol;
use cls\RequestError;

if (count(debug_backtrace()) == 0) {
  require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}

function login(
  App   $app,
  array $post_data,
): Account|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  if (!isset($post_data["username_or_email"])) {
    return new RequestError(
      dev_message: "\$post_data[\"username_or_email\"] not set",
      code: RequestError::BAD_REQUEST,
    );
  }

  if (!isset($post_data["password"])) {
    return new RequestError(
      dev_message: "\$post_data[\"password\"] not set",
      code: RequestError::BAD_REQUEST,
    );
  }

  $username_or_email = $post_data["username_or_email"];
  $password = $post_data["password"];

  $account = Account::get_user_by_username_or_mail(
    username_or_email: $username_or_email,
    app: $app
  );

  if ($account === null) {
    return new RequestError(
      dev_message: "No account found with username or email: $username_or_email",
      code: RequestError::USER_INPUT_ERROR,
    );
  }

  if (!password_verify($password, $account->password)) {
    return new RequestError(
      dev_message: "Wrong password",
      code: RequestError::USER_INPUT_ERROR,
    );
  }

  $app->login($account);
  

  return $account;

}


return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: login(...),
  app: App::get(),
);