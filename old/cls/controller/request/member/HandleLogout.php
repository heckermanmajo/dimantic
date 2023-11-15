<?php

namespace cls\controller\request\member;

use cls\RequestError;

class HandleLogout {
  static function execute(): null|RequestError {
    session_destroy();
    return null;
  }
}