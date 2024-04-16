<?php

namespace src\core\exceptions;

use src\core\Request;

class RequestException extends \Exception {
  public Request $request_that_failed;

  function set_request(Request $request_that_failed): void {
    $this->request_that_failed = $request_that_failed;
  }

}