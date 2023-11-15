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

function start_dialoge(
  App   $app,
  array $post_data,
): Dialogue|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  if (!isset($post_data["dialogue_id"])) {
    return new RequestError(
      dev_message: "dialogue_id not set",
      code: RequestError::BAD_REQUEST,
    );
  }

  $dialogue = Dialogue::get_by_id(
    $app->get_database(),
    (int) $_POST["dialogue_id"]
  );

  # todo: handle bad

  $dialogue->state = Dialogue::STATE_OPEN;

  $dialogue->save($app->get_database());

  return $dialogue;

}


return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: start_dialoge(...),
  app: App::get(),
);