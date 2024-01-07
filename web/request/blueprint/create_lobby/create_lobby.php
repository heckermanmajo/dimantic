<?php

declare(strict_types=1);

use cls\App;
use cls\data\conversation_blue_print\Lobby;
use cls\data\conversation_blue_print\LobbyMembership;
use cls\Protocol;
use cls\RequestError;


if (count(debug_backtrace()) == 0) {
  require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


/**
 * This request creates a new lobby for a conversation blueprint.
 *
 * @param array $post_data
 * @return Lobby|RequestError
 */
function create_lobby(
  array $post_data,
): Lobby|RequestError {

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  
  try {
    
    $app = App::get();

    if (!App::get()->somebody_logged_in()) {
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    if (!isset($post_data['blue_print_id'])) {
      return new RequestError(
        dev_message: "\$post_data['blue_print_id'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    # todo: check that blueprint exists and is not in use and user is allowed to create lobby

    $lobby = new Lobby();
    $lobby->conversation_blueprint_id = (int)$post_data['blue_print_id'];
    $lobby->author_id = $app->get_currently_logged_in_account()->id;

    $lobby->save($app->get_database());

    # todo: join lobby yourself
    $lobby_membership = new LobbyMembership();
    $lobby_membership->lobby_id = $lobby->id;
    $lobby_membership->account_id = $app->get_currently_logged_in_account()->id;
    $lobby_membership->save($app->get_database());

    # todo: match users to this lobby via interests, tags, embeddings, etc. ...

    return $lobby;

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
  function: create_lobby(...)
);