<?php

namespace src\global\action;

use src\core\Action;

/**
 * @see src\global\compositions\GetLastRequestData
 */
class SaveRequestDataForNextRequestAction extends Action{

  function __construct() {
    # todo: implement the passing in of errors and logs
  }

  function is_allowed(): bool {
      return true;
  }

  function execute(): void {
    $_SESSION['LAST_REQUEST'] = [
      'post' => $_POST,
      'get' => $_GET,
      'session' => $_SESSION,
      'cookie' => $_COOKIE,
      'files' => $_FILES
    ];
  }


}