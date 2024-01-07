<?php
# entweder man erstellt einen space
# als room eines anderen
#  -> darf man das? (rechte checken)
# order toplevel

declare(strict_types=1);

use cls\App;
use cls\data\space\Space;
use cls\data\space\SpaceMembership;
use cls\Protocol;
use cls\RequestError;


if (count(debug_backtrace()) == 0) {
  require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


/**
 * This request creates a new space.
 *
 * @param App $app
 * @param array<string,string> $post_data
 * @return Space|RequestError
 */
function delete_space(
  App   $app,
  array $post_data,
): null|RequestError {

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  try {

    if (!$app->somebody_logged_in()) {
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    if (!isset($post_data['space_id'])) {
      return new RequestError(
        dev_message: "\$post_data['space_id'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $space = Space::get_by_id(
      pdo: $app->get_database(),
      id: (int) $post_data['space_id'],
    );

    if ($space === null) {
      return new RequestError(
        dev_message: "Space not found.",
        code: RequestError::NOT_FOUND,
      );
    }

    if (!$space->current_user_as_delete_rights($app)) {
      return new RequestError(
        dev_message: "You are not the owner of this space.",
        code: RequestError::RULE_ERROR,
      );
    }

    // todo: only archive the space and all its content

    // but for now just delete it
    $space->delete($app->get_database());

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
  function: delete_space(...),
  app: App::get(),
);