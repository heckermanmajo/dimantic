<?php

namespace cls\data\conversation_blue_print;

use cls\App;
use cls\DataClass;
use Exception;

class LobbyMembership extends DataClass {
  var int $lobby_id = 0;
  var int $account_id = 0;

  /**
   * Retrieves the memberships of a lobby.
   *
   * @param App $app The application object.
   * @param int $lobby_id The ID of the lobby to retrieve memberships from.
   * @return array<static> An array containing the memberships of the lobby.
   * @throws Exception
   */
  static function get_memberships_of_lobby(
    App $app,
    int $lobby_id,
  ): array {
    return self::get_array(
      $app->get_database(),
      "SELECT * FROM LobbyMembership WHERE lobby_id = ?",
      [$lobby_id]
    );
  }

  /**
   * Checks if a given user is a member of a lobby.
   *
   * @param App $app The instance of the app providing access to its functionality.
   * @param int $lobby_id The ID of the lobby to check for the user's membership.
   *
   * @return bool Returns true if the user is a member of the lobby, false otherwise.
   * @throws Exception
   */
  static function is_given_user_member_of_lobby(
    App $app,
    int $lobby_id,
  ){
    $memberships = self::get_memberships_of_lobby(
      $app,
      $lobby_id
    );
    foreach ($memberships as $membership) {
      if ($membership->account_id === $app->get_currently_logged_in_account()->id) {
        return true;
      }
    }
    return false;
  }


}