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
 * This request creates a new space.
 *
 * @param App $app
 * @param array $post_data
 * @return Space|RequestError
 */
function create_new_space(
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

    if (!isset($post_data['content'])) {
      return new RequestError(
        dev_message: "\$post_data['content'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $content = $post_data['content'];

    # don't insert the same space twice
    $existing_spaces = Space::getByContent(
      $app,
      $content,
    );
    # todo_ possibly check author_id as well
    if (count($existing_spaces) > 0) {
      return new RequestError(
        dev_message: "This space already exists.",
        code: RequestError::RULE_ERROR,
      );
    }

    $space = new Space();

    $space->content = $content;
    $space->author_id = $app->get_currently_logged_in_account()->id;
    $space->created_at = time();
    $space->save($app->get_database());
    
    # Create a membership for the author
    $membership = new SpaceMembership();
    $membership->member_id = $app->get_currently_logged_in_account()->id;
    $membership->space_id = $space->id;
    $membership->role = SpaceMembership::ROLE_CONSUL;
    $membership->created_at = time();
    $membership->save($app->get_database());

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
  function: create_new_space(...),
  app: App::get(),
);