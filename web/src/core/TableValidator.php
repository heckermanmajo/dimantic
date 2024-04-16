<?php

namespace src\core;

use src\core\exceptions\BadValue;
use src\core\exceptions\RequestException;
use src\core\exceptions\TableFieldException;

interface TableValidator {

  /**
   * @param bool $throw
   * @param bool $in_request
   * @return array<BadValue|TableFieldException>|null
   */
  function validate(
    bool $throw = false,
    bool $in_request = false
  ): array|null;

  function validateContext(
    bool $throw = false,
    bool $in_request = false
  ): array|null;


}