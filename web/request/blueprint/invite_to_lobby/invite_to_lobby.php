<?php

declare(strict_types=1);

use cls\App;
use cls\data\account\Account;

use cls\data\account\news\InviteToLobbyNewsEntry;
use cls\data\conversation_blue_print\ConversationBluePrint;
use cls\data\conversation_blue_print\Lobby;
use cls\Protocol;
use cls\RequestError;


if (count(debug_backtrace()) == 0) {
  require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


/**
 * This request invites a user to a lobby.
 * -> TODO: Make this work with just emails
 *
 * @param App $app
 * @param array $post_data
 * @return InviteToLobbyNewsEntry|RequestError
 */
function invite_to_lobby(
  App   $app,
  array $post_data,
): InviteToLobbyNewsEntry|RequestError {

  # todo: IF YOU ARE THE CREATOR OF THE LOBBY, YOUR INVITATIONS CREATE A NEWS ENTRY AND A PLACEHOLDER FOR THE INVITED USER
  #       OTHERWISE YOU WILL GET PROBLEMS SINCE OTHER MEMBERS CAN JUST JOIN THE LOBBY AND FOR THE INVITED USER THERE IS NO
  #       PLACE

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  try {

    if (!$app->somebody_logged_in()) {
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    if (!isset($post_data['id_username_or_email'])) {
      return new RequestError(
        dev_message: "\$post_data['id_username_or_email'] not set",
        code: RequestError::BAD_REQUEST,
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

    if ($lobby->lobby_is_full($app)) {
      return new RequestError(
        dev_message: "Lobby is full",
        code: RequestError::RULE_ERROR,
      );
    }

    $blueprint = ConversationBluePrint::get_by_id(
      $app->get_database(),
      $lobby->conversation_blueprint_id
    );

    $inviter_is_owner_of_blueprint
      = $blueprint->author_id === $app->get_currently_logged_in_account()->id
      ? 1 : 0;

    $value = $post_data["id_username_or_email"];

    if (is_numeric($value)) {
      # this is a user id

      $user = Account::get_by_id(
        $app->get_database(),
        (int)$value
      );

      if ($user === null) {
        return new RequestError(
          dev_message: "User with id $value does not exist.",
          code: RequestError::NOT_FOUND,
        );
      }

      $news_entry = new InviteToLobbyNewsEntry();
      $news_entry->inviter_account_id = $app->get_currently_logged_in_account()->id;
      $news_entry->author_of_invitation_is_owner_of_lobby_blueprint = $inviter_is_owner_of_blueprint;
      $news_entry->lobby_id = $lobby->id;
      $news_entry->blueprint_id = $blueprint->id;
      $news_entry->possible_account_id = $user->id;
      $news_entry->save($app->get_database());

    }
    else if (str_contains($value, "@")) {
      # this is an email
      # check if that user mail is already registered

      $user = Account::get_by_email(
        app: $app,
        email: $value
      );

      if ($user === null) {
        // user does not YET exist
        // so we create a news entry with the email
        $news_entry = new InviteToLobbyNewsEntry();
        $news_entry->inviter_account_id = $app->get_currently_logged_in_account()->id;
        $news_entry->author_of_invitation_is_owner_of_lobby_blueprint = $inviter_is_owner_of_blueprint;
        $news_entry->lobby_id = $lobby->id;
        $news_entry->blueprint_id = $blueprint->id;
        $news_entry->potential_account_email = $value;
        $news_entry->save($app->get_database());

        // todo: send email to email-address

      }
      else{
        // user already exists
        // so we create a news entry with the user id
        $news_entry = new InviteToLobbyNewsEntry();
        $news_entry->inviter_account_id = $app->get_currently_logged_in_account()->id;
        $news_entry->author_of_invitation_is_owner_of_lobby_blueprint = $inviter_is_owner_of_blueprint;
        $news_entry->lobby_id = $lobby->id;
        $news_entry->blueprint_id = $blueprint->id;
        $news_entry->possible_account_id = $user->id;
        $news_entry->save($app->get_database());
      }

    }
    else {
      # this is a username
      $user = Account::get_user_by_username(
        username: $value,
        app: $app
      );

      if ($user === null) {
        return new RequestError(
          dev_message: "User with username $value does not exist.",
          code: RequestError::NOT_FOUND,
        );
      }

      $news_entry = new InviteToLobbyNewsEntry();
      $news_entry->inviter_account_id = $app->get_currently_logged_in_account()->id;
      $news_entry->author_of_invitation_is_owner_of_lobby_blueprint = $inviter_is_owner_of_blueprint;
      $news_entry->lobby_id = $lobby->id;
      $news_entry->blueprint_id = $blueprint->id;
      $news_entry->possible_account_id = $user->id;
      $news_entry->save($app->get_database());

    }

    return $news_entry;

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
  function: invite_to_lobby(...),
  app: App::get(),
);