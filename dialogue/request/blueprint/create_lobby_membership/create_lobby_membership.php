<?php

declare(strict_types=1);

use cls\App;
use cls\data\conversation_blue_print\Lobby;
use cls\data\conversation_blue_print\LobbyMembership;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMembership;
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
function create_lobby_membership(
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
      (int)$post_data['lobby_id']
    );

    if ($lobby === null) {
      return new RequestError(
        dev_message: "Lobby not found",
        code: RequestError::NOT_FOUND,
      );
    }

    # todo: check rights and stuff

    if(LobbyMembership::is_given_user_member_of_lobby($app, $lobby->id)){
      return new RequestError(
        dev_message: "You are already a member of this lobby.",
        code: RequestError::RULE_ERROR,
      );
    }

    $lobby_membership = new LobbyMembership();
    $lobby_membership->lobby_id = $lobby->id;
    $lobby_membership->account_id = $app->get_currently_logged_in_account()->id;

    $lobby_membership->save($app->get_database());

    if($lobby->lobby_has_enough_members($app)){
      // create the conversation from the blueprint

      $dialogue = new Dialogue();
      $dialogue->blue_print_id = $lobby->conversation_blueprint_id;
      $dialogue->save($app->get_database());

      $lobby_memberships = LobbyMembership::get_memberships_of_lobby(
        $app,
        $lobby->id
      );

      foreach ($lobby_memberships as $lobby_membership) {
        $user_to_create_membership_for = $lobby_membership->account_id;
        $dialogue_membership = new DialogueMembership();
        $dialogue_membership->dialogue_id = $dialogue->id;
        $dialogue_membership->account_id = $user_to_create_membership_for;
        $dialogue_membership->state = DialogueMembership::STATE_ACTIVE;
        $dialogue_membership->save($app->get_database());
        # todo: create news for the users
      }

    }

    # todo: if the lobby is now FULL_: DELETE THE LOBBY AND START THE CONVERSATION

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
  function: create_lobby_membership(...),
  app: App::get(),
);