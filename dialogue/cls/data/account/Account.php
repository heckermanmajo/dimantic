<?php
declare(strict_types=1);

namespace cls\data\account;

use cls\App;
use cls\DataClass;
use Exception;


/**
 * Dimantic does not have "Users" but Members.
 *
 * Every Member has an account.
 * The account stores the login data and the profile data.
 *
 */
class Account extends DataClass {

  ###########################################################################
  #                                                                         #
  #  Properties                                       #
  #                                                                         #
  ###########################################################################
  var string $name = "";
  var string $email = "";
  var string $password = "";
  var string $time_zone = "Europe/Berlin";
  var string $language = "de";
  var string $content = "";
  var string $create_date = "";

  #################################
  ###### Joined Values      #######
  #################################

  #################################
  ###### Property-functions #######
  #################################

  /**
   * @inheritdoc
   * @throws Exception
   */
  static function check_value(
    string $field_name,
    mixed  $value,
    App    $app
  ): string|null {

    switch ($field_name) {

      case "name":
        if (strlen($value) < 3) {
          return "name must be at least 3 characters long";
        }
        if (strlen($value) > 20) {
          return "name must be at most 20 characters long";
        }
        if (!preg_match("/^[a-zA-Z0-9]+$/", $value)) {
          return "name must only contain letters and numbers";
        }
        // check that the account name is not already taken
        $account_is_taken = static::get_one(
          pdo: $app->get_database(),
          sql: "SELECT * FROM Account WHERE `name` = :name",
          params: [
            "name" => $value,
          ]
        );
        if ($account_is_taken !== null) {
          return "Name is already taken";
        }
        return null;

      case "email":
        # todo: finish all fields ...
    }
    return null;
  }

  /**
   * Retrieves an Account object based on the provided email address.
   *
   * @param App $app The application instance.
   * @param string $email The email address used to search for the Account.
   *
   * @return Account|null The Account object found for the specified email address,
   *         or null if none exists.
   * @throws Exception
   */
  static function get_by_email(
    App $app,
    string $email
  ): ?Account {
    return static::get_one(
      pdo: $app->get_database(),
      sql: "SELECT * FROM Account WHERE email = ?",
      params: [$email],
      throw_on_null: false
    );
  }

  ###########################################################################
  #                                                                         #
  #  Model-Queries                                                          #
  #                                                                         #
  ###########################################################################

  /**
   * Retrieves a user account by username or email.
   *
   * @param string $username_or_email The username or email to search for in the Account table.
   * @param App $app The application instance.
   * @return Account|null The matching user account, or null if no match is found.
   * @throws Exception
   */
  static function get_user_by_username_or_mail(
    string $username_or_email,
    App    $app
  ): Account|null {
    return static::get_one(
      pdo: $app->get_database(),
      sql: "SELECT * FROM Account WHERE `name` = :name OR email = :email",
      params: [
        "name" => $username_or_email,
        "email" => $username_or_email,
      ]
    );
  }

  /**
   * Get a user account by username.
   *
   * @param string $username The username of the user.
   * @param App $app The application instance.
   * @return Account|null The user account object if found, or null if not found.
   * @throws Exception
   */
  static function get_user_by_username(
    string $username,
    App    $app
  ): Account|null {
    return static::get_one(
      pdo: $app->get_database(),
      sql: "SELECT * FROM Account WHERE `name` = :name",
      params: [
        "name" => $username,
      ]
    );
  }

  /**
   * @param int $offset
   * @param int $limit
   * @param App $app
   * @return array<Account>
   */
  static function get_all_accounts(
    int $offset, int $limit, App $app
  ): array {
    return static::get_array(
      pdo: $app->get_database(),
      sql: "SELECT * FROM Account LIMIT :offset, :limit",
      params: [
        "offset" => $offset,
        "limit" => $limit,
      ]
    );
  }

  ###########################################################################
  #                                                                         #
  #  Logic & Controller                                                     #
  #                                                                         #
  ###########################################################################
  static function get_members_by_search_string() { }

  ###########################################################################
  #                                                                         #
  #  Views                                                                  #
  #                                                                         #
  ###########################################################################

  /**
   * Returns the HTML for the account card.
   */
  function get_display_card(App $app): string {
    ob_start();
    ?>
    <div class="w3-card-4 w3-margin w3-padding">
      <h3>
        <?= $this->get_gravtar_profile_image() ?>
        <?= $this->name ?>
      </h3>
      <pre><?= $this->content ?></pre>
      <br>
    </div>
    <?php
    return ob_get_clean();
  }

  /** @return string The HTML for the profile image based on the email address (Gravatar) */
  function get_gravtar_profile_image(int $size = 40): string {
    return static::get_gravatar(
      email: $this->email,
      size: $size,
      default: 'wavatar',
      img: true,
      attributes: [
        "class" => "w3-circle",
      ]
    );
  }

  /**
   * Get either a Gravatar URL or complete image tag for a specified email address.
   *
   * @param string $email The email address
   * @param int $size Size in pixels, defaults to 80px [ 1 - 2048 ]
   * @param string $default Default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
   * @param string $rating Maximum rating (inclusive) [ g | pg | r | x ]
   * @param bool $img True to return a complete IMG tag False for just the URL
   * @param array $attributes Optional, additional key/value attributes to require in the IMG tag
   * @return String containing either just a URL or a complete image tag
   * @source https://gravatar.com/site/implement/images/php/
   */
  private static function get_gravatar(
    string $email,
    int    $size = 80,
    string $default = 'mp',
    string $rating = 'x',
    bool   $img = false,
    array  $attributes = array()
  ) {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5(strtolower(trim($email)));
    $url .= "?s=$size&d=$default&r=$rating";
    if ($img) {
      $url = '<img src="' . $url . '"';
      foreach ($attributes as $key => $val)
        $url .= ' ' . $key . '="' . $val . '"';
      $url .= ' />';
    }
    return $url;
  }

}