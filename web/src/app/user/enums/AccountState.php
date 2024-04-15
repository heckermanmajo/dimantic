<?php

namespace src\app\user\enums;

enum AccountState: int {

  case ACTIVE = 1;
  case INACTIVE = 2;
  case DELETED = 3;

  case FROZEN = 4;

  case PLATFORM_ADMIN = 5;

}