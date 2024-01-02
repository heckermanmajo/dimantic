<?php

declare(strict_types=1);

use cls\App;
use cls\data\conversation_blue_print\Lobby;
use cls\data\conversation_blue_print\LobbyMembership;
use cls\Protocol;
use cls\RequestError;


if (count(debug_backtrace()) == 0) {
  include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


/**
 * This request creates a new lobby membership.
 *
 * @param App $app
 * @param array $post_data
 * @return LobbyMembership|RequestError
 */
function delete_lobby_membership(
  App   $app,
  array $post_data,
): LobbyMembership|RequestError {

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  try {

    if (!$app->somebody_logged_in()) {
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    if (!isset($post_data['lobby_id'])) {
      return new RequestError(
        dev_message: "\$post_data['lobby_id'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $lobby = Lobby::get_by_id(
      $app->get_database(),
      (int)$post_data['lobby_membership_id']
    );

    if ($lobby === null) {
      return new RequestError(
        dev_message: "Lobby not found",
        code: RequestError::NOT_FOUND,
      );
    }

    # todo: check rights and stuff

    $lobby_membership = LobbyMembership::get_by_id(
      $app->get_database(),
      (int)$post_data['lobby_membership_id']
    );

    if ($lobby_membership === null) {
      return new RequestError(
        dev_message: "Lobby membership not found",
        code: RequestError::NOT_FOUND,
      );
    }

    $lobby_membership->delete($app->get_database());

    return $lobby_membership;

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
  function: delete_lobby_membership(...),
  app: App::get(),
);