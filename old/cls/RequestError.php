<?php

namespace cls;

use cls\data\ErrorDBEntry;

class RequestError {
  const USER_INPUT_ERROR = "USER_INPUT_ERROR";
  const SYSTEM_ERROR = "SYSTEM_ERROR";
  const BAD_REQUEST = "BAD_REQUEST";
  const NOT_FOUND = "NOT_FOUND";

  function __construct(
    public string      $dev_message,
    public string      $code,
    public string      $user_message = "",
    public array      $extra_data = [],
    public ?\Throwable $e = null
  ) {
    // ...
  }

  function create_error_db_entry_class_instance(): ErrorDBEntry {
    // ...
  }

}