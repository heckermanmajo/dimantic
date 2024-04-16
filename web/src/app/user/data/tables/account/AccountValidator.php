<?php

namespace src\app\user\data\tables\account;

use src\core\exceptions\TableFieldValueException;
use src\core\TableValidator;

class AccountValidator implements TableValidator {
  public function __construct(
    private Account $account
  ) {
  }


  function validate(bool $throw = false, bool $in_request = false): array|null {
    return null;
  }

  function validateContext(bool $throw = false, bool $in_request = false): array|null {
    return null;
  }
}