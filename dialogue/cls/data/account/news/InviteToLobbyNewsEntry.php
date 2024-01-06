<?php

namespace cls\data\account\news;

use cls\App;
use cls\DataClass;
use cls\GetDisplayCardInterface;
use Exception;

/**
 * This news-entry is created when a user is invited to a lobby.
 */
class InviteToLobbyNewsEntry extends DataClass implements GetDisplayCardInterface{

  /**
   * @var int The account id of the user who invited the other user.
   */
  var int $inviter_account_id = 0;

  /**
   * @var int The lobby id of the lobby the user is invited to.
   */
  var int $lobby_id = 0;

  /**
   * @var int If this is the case, the invitation creates a "blocked" membership for
   *      the invited user.
   */
  var int $author_of_invitation_is_owner_of_lobby_blueprint = 0;

  /**
   * @var int The account id of the user who is invited to the lobby.
   */
  var int $possible_account_id = 0;

  /**
   * @var int The blueprint id of the lobby the user is invited to.
   */
  var int $blueprint_id = 0;

  /**
   * If you invite a user via email, the email is written  into
   * this field. If a new user is registered via this email,
   * the account_id is written into account_id.
   * @var string
   */
  var string $potential_account_email = "";

  /**
   * If the news entry has been read by the account.
   * 0 = not read
   * 1 = read
   */
  var int $read = 0;

  /**
   * Retrieves news entries related to a specific account.
   *
   * @param App $app The application instance.
   * @param int $account_id The account ID.
   * @return array An array of news entry objects.
   * @throws Exception If an error occurs while retrieving the news entries.
   */
  static function get_news_for_account(
    App $app,
    int $account_id,
  ): array {

    return self::get_array(
      $app->get_database(),
      "SELECT * FROM InviteToLobbyNewsEntry WHERE possible_account_id = ?",
      [$account_id]
    );

  }

  /**
   * Retrieves the HTML markup for the display card of an invitation to a conversation.
   *
   * This method generates the HTML markup for a display card that represents an invitation to a conversation.
   * The card includes information about the inviter and a brief description of the conversation.
   *
   * @return string The HTML markup for the display card.
   */
  function get_display_card(App $app): string {
    ob_start();

    $link = "/blueprint.php?id=" . $this->blueprint_id . "#lobby-" . $this->lobby_id;
    ?>
      <div class="sketch-card">
        <h3>You are invited into a conversation!</h3>
        <pre>
          by ....
          about: shorthand of the conversation-description, bla bla
        </pre>
        <a href="<?=$link?>"> Go to Blueprint ... </a>
      </div>
    <?php
    return ob_get_clean();
  }
}