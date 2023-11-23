<?php
declare(strict_types=1);

namespace cls\data\account;

use cls\App;
use cls\DataClass;


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

  ###########################################################################
  #                                                                         #
  #  Model-Queries                                                          #
  #                                                                         #
  ###########################################################################

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
      <a class="button" href="/dialogue.php?partner_id=<?= $this->id ?>"> Start dialogue with this person </a>

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
   * @param array $attributes Optional, additional key/value attributes to include in the IMG tag
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