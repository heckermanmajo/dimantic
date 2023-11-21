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

function logout(
  App   $app,
  array $post_data,
): null|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  $app->logout();
  return null;
}


return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: logout(...),
  app: App::get(),
);