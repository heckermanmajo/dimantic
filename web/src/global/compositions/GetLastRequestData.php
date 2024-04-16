<?php

namespace src\global\compositions;

use src\core\Composition;

/**
 * This composition provides the data the last request has gotten and
 * also its logs and stuff.
 *
 * This is necessary, since a request always redirects and the logs/errors and
 * post/get data is lost, if we don't write it into the session.
 *
 * This composition provides access to these session fields.
 *
 * @see src\global\action\SaveRequestDataForNextRequest
 *
 */
class GetLastRequestData extends Composition {

  static function get_last_post_field_by_name(string $name): string {
    return $_SESSION['LAST_REQUEST']['post'][$name] ?? "";
  }

  static function get_last_path(): string {
    return $_SERVER["HTTP_REFERER"] ?? "/index.php";
  }

}