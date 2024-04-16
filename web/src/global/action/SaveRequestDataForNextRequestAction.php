<?php

namespace src\global\action;

use src\core\Action;
use src\core\Component;
use src\core\Request;

/**
 * @see src\global\compositions\GetLastRequestData
 */
class SaveRequestDataForNextRequestAction extends Action{

  function __construct(
    private ?Component $error_card = null,
    private Request $last_request,
  ) {
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
      'files' => $_FILES,
      "last_request" => $this->last_request,
      "error_card" => $this->error_card,
    ];
  }


}