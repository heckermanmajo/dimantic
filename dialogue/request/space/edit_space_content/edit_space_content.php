<?php
# entweder man erstellt einen space
# als room eines anderen
#  -> darf man das? (rechte checken)
# order toplevel

declare(strict_types=1);

use cls\App;
use cls\data\space\Space;
use cls\Protocol;
use cls\RequestError;


if (count(debug_backtrace()) == 0) {
  include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


/**
 * This request creates a new space.
 *
 * @param App $app
 * @param array<string,string> $post_data
 * @return Space|RequestError
 */
function edit_space_content(
  App   $app,
  array $post_data,
): Space|RequestError {

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  try {

    if (!$app->somebody_logged_in()) {
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    if (!isset($post_data['id'])) {
      return new RequestError(
        dev_message: "\$post_data['id'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    if (!isset($post_data['content'])) {
      return new RequestError(
        dev_message: "\$post_data['content'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $space = Space::get_by_id(
      pdo: $app->get_database(),
      id: (int) $post_data['id'],
    );

    $am_i_allowed_to_edit = $space->author_id == $app->get_currently_logged_in_account()->id;

    if (!$am_i_allowed_to_edit) {
      return new RequestError(
        dev_message: "You are not allowed to edit this space.",
        code: RequestError::RULE_ERROR,
      );
    }

    $space->content = $post_data['content'];

    $space->save($app->get_database());

    return $space;

  }

  catch (Exception $e) {
    return new RequestError(
      dev_message: $e->getMessage(),
      code: RequestError::SYSTEM_ERROR,
    );
  }

}


return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: edit_space_content(...),
  app: App::get(),
);