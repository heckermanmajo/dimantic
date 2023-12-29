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
  include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


/**
 * This request deletes a space membership.
 *
 * @param App $app
 * @param array<string,string> $post_data
 * @return Space|RequestError
 */
function delete_space_membership(
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

    if (!isset($post_data['space_id'])) {
      return new RequestError(
        dev_message: "\$post_data['space_id'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    if (!isset($post_data['space_membership_id'])) {
      return new RequestError(
        dev_message: "\$post_data['space_membership_id'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $space = Space::get_by_id(
      pdo: $app->get_database(),
      id: (int)$post_data['space_id'],
    );

    if ($space === null) {
      return new RequestError(
        dev_message: "Space not found.",
        code: RequestError::NOT_FOUND,
      );
    }
    $space_membership_id = (int)$post_data['space_membership_id'];

    $space_membership = SpaceMembership::get_by_id(
      pdo: $app->get_database(),
      id: $space_membership_id,
    );

    if ($space_membership === null) {
      return new RequestError(
        dev_message: "Space membership not found.",
        code: RequestError::NOT_FOUND,
      );
    }

    # todo: check if the user is allowed to delete this membership

    # for now only check if the current user is owner of the membership or the space
    $am_i_allowed_to_delete =
      $space_membership->member_id == $app->get_currently_logged_in_account()->id
      || $space->author_id == $app->get_currently_logged_in_account()->id;

    if (!$am_i_allowed_to_delete) {
      return new RequestError(
        dev_message: "You are not allowed to delete this space membership.",
        code: RequestError::RULE_ERROR,
      );
    }

    # todo: add news entries ...

    $space_membership->delete($app->get_database());

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
  function: delete_space_membership(...),
  app: App::get(),
);