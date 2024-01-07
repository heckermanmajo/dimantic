<?php

namespace cls\data\conversation_blue_print;

use cls\App;
use cls\data\account\Account;
use cls\DataClass;
use Exception;

/**
 * Class Lobby
 *
 * Represents a lobby with information about the author and conversation blueprint.
 */
class Lobby extends DataClass {
  var int $author_id = 0;
  var int $conversation_blueprint_id = 0;

  /**
   * @throws Exception
   */
  static function get_lobbies_of_conversation_blueprint(
    App $app,
    int $conversation_blueprint_id,
  ): array {
    return self::get_array(
      $app->get_database(),
      "SELECT * FROM Lobby WHERE conversation_blueprint_id = ?",
      [$conversation_blueprint_id]
    );
  }

  /**
   * Determines if a lobby has enough members based on the associated blueprint.
   *
   * @param App $app The application instance.
   *
   * @return bool Returns true if the lobby has enough members, false otherwise.
   *
   * @throws Exception If an error occurs while fetching the associated blueprint or memberships.
   */
  function lobby_has_enough_members(App $app): bool {
    # as many memberships exist as there are set in the needed members field of the associated blueprint
    $blueprint = ConversationBlueprint::get_by_id(
      $app->get_database(),
      $this->conversation_blueprint_id
    );
    $memberships = LobbyMembership::get_memberships_of_lobby(
      $app,
      $this->id
    );
    $min_needed_member_num = $blueprint->min_number_of_users;
    return count($memberships) >= $min_needed_member_num;
  }

  /**
   * Checks if the lobby is full based on the number of max memberships.
   *
   * @param App $app The application instance.
   *
   * @return bool Returns true if the lobby is full, false otherwise.
   *
   * @throws Exception If an error occurs while retrieving the blueprint or memberships.
   */
  function lobby_is_full(App $app): bool {
    # as many memberships exist as there are set in the needed members field of the associated blueprint
    $blueprint = ConversationBlueprint::get_by_id(
      $app->get_database(),
      $this->conversation_blueprint_id
    );
    $memberships = LobbyMembership::get_memberships_of_lobby(
      $app,
      $this->id
    );
    $max_needed_member_num = $blueprint->max_number_of_users;
    return count($memberships) >= $max_needed_member_num;
  }

  /**
   * @throws Exception
   */
  function display_card(App $app): string {
    ob_start();
    $blueprint = ConversationBlueprint::get_by_id(
      $app->get_database(),
      $this->conversation_blueprint_id
    );
    $memberships = LobbyMembership::get_memberships_of_lobby(
      $app,
      $this->id
    );
    ?>
    <div class="w3-card-4 w3-padding" id="lobby-<?= $this->id ?>">

      <h4> Lobby </h4>
      <!-- <pre><?= json_encode($this, JSON_PRETTY_PRINT) ?></pre>-->

      <?php

      if ($app->executed_action === "create_lobby_membership") {
        if ($app->action_error != null) {
          echo $app->action_error->get_error_card($app);
        }
      }

      ?>

      <div class="w3-row">

        <form method="post" class="w3-col m3 l3 s12">
          <input type="hidden" name="action" value="create_lobby_membership">
          <input type="hidden" name="lobby_id" value="<?= $this->id ?>">
          <button class="sketch-button"> JOIN LOBBY</button>
        </form>


        <?php
        $invite_error = false;
        if ($app->executed_action === "invite_to_lobby") {
          if ($app->action_error != null) {
            echo $app->action_error->get_error_card($app);
            $invite_error = true;
          }
          else {
            echo "<pre>Invitation sent!</pre>";
          }
        }
        ?>
        <form method="post" class="w3-rest">
          <input type="hidden" name="action" value="invite_to_lobby">
          <input type="hidden" name="lobby_id" value="<?= $this->id ?>">
          <label>
            <input
              placeholder="mail, id, or username"
              type="text"
              name="id_username_or_email"
              value="<?= $invite_error ? $_POST["id_username_or_email"] : "" ?>">
          </label>
          <button class="sketch-button"> Invite to LOBBY</button>
        </form>

      </div>

      Joined: <br>
      <?php

      foreach ($memberships as $member) {
        # todo: improve.. this is performance wise not good
        $member_account = Account::get_by_id(
          $app->get_database(),
          $member->account_id
        );

        ?>
        <div class="w3-card-4 w3-padding">
          <div>
            <?= $member_account->get_gravtar_profile_image() ?>
            <b>
              <?= $member_account->name ?>
            </b>
          </div>
        </div>
        <?php

      }

      ?>

    </div>
    <?php
    return ob_get_clean();
  }

  /**
   * @throws Exception
   */
  static function get_number_of_lobbies_for_blueprint(int $blueprint_id): int {
    return static::get_count(
      App::get()->get_database(),
      "SELECT COUNT(*) FROM Lobby WHERE conversation_blueprint_id = ?",
      [$blueprint_id]
    );
  }
}