<?php

namespace cls\data;

use cls\DataClass;

class ErrorDBEntry extends DataClass {
  var int $account_id = 0;
  var string $account_message = "";
  var string $type = "";
  var string $user_message = "";
  var string $traceback_as_string = "";
  var string $create_date = "";
}