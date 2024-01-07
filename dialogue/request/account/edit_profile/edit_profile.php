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

/**
 * Edit the profile of a logged-in account.
 *
 * @param App $app The application instance.
 * @param array $post_data The profile data to be edited.
 * @return Account|RequestError The edited account or an error if the edit failed.
 * @throws Exception
 */
function edit_profile(
  App   $app,
  array $post_data,
): Account|RequestError {

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  if(!$app->somebody_logged_in()){
    return new RequestError(
      dev_message: "You are not logged in.",
      code: RequestError::RULE_ERROR,
    );
  }

  if (!isset($post_data["content"])) {
    return new RequestError(
      dev_message: "\$post_data[\"content\"] not set",
      code: RequestError::BAD_REQUEST,
    );
  }

  $account = $app->get_currently_logged_in_account();

  $todo("No checks for content done.");
  $account->content = $post_data["content"];
  $account->save($app->get_database());

  $log("Re-login of account, after account->content change.");
  $app->login($account);

  return $account;

}


return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: edit_profile(...),
  app: App::get(),
);