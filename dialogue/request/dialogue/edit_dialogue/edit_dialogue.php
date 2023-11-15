<?php
declare(strict_types=1);

use cls\App;
use cls\data\account\Account;
use cls\data\dialoge\Dialogue;
use cls\Protocol;
use cls\RequestError;

if (count(debug_backtrace()) == 0) {
  include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}

function edit_dialogue(
  App   $app,
  array $post_data,
): Dialogue|RequestError {

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__);

  if (!isset($post_data["dialogue_id"])) {
    return new RequestError(
      dev_message: "\$post_data[\"dialogue_id\"] not set",
      code: RequestError::BAD_REQUEST,
    );
  }

  if (!isset($post_data["content"])) {
    return new RequestError(
      dev_message: "\$post_data[\"content\"] not set",
      code: RequestError::BAD_REQUEST,
    );
  }

  $todo("NO ACCESS CHECKS DONE ");
  $dialogue = Dialogue::get_by_id(
    $app->get_database(),
    (int)$post_data["dialogue_id"],
  );

  $log("check that dialogue is not yet started");

  if ($dialogue->state !== Dialogue::STATE_NOT_YET_STARTED) {
    return new RequestError(
      dev_message: "Dialogue is already started.",
      code: RequestError::RULE_ERROR,
    );
  }

  $error_or_null = Dialogue::check_value(
    field_name: "content",
    value: $post_data["content"],
    app: $app
  );
  if ($error_or_null !== null) {
    return new RequestError(
      dev_message: $error_or_null,
      code: RequestError::USER_INPUT_ERROR,
    );
  }

  $dialogue->content = $post_data["content"];

  $dialogue->save($app->get_database());

  return $dialogue;

}


return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: edit_dialogue(...),
  app: App::get(),
);