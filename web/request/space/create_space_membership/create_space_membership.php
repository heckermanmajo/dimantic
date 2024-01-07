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
 * @param array<string,string> $post_data
 * @return Space|RequestError
 */
function create_space_membership(
  array $post_data,
): Space|RequestError {

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  try {
    
    $app = App::get();

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

    $space_memberships = SpaceMembership::get_all_memberships_of_space(
      space_id: $space->id,
    );

    foreach ($space_memberships as $space_membership) {
      if ($space_membership->member_id == $app->get_currently_logged_in_account()->id) {
        return new RequestError(
          dev_message: "You are already a member of this space.",
          code: RequestError::RULE_ERROR,
        );
      }
    }

    $new_space_membership = new SpaceMembership();
    $new_space_membership->member_id = $app->get_currently_logged_in_account()->id;
    $new_space_membership->space_id = $space->id;
    $new_space_membership->role = SpaceMembership::ROLE_MEMBER;
    $new_space_membership->created_at = time();

    # todo: add news entries ...

    $new_space_membership->save($app->get_database());

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
  function: create_space_membership(...),
);