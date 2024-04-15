<?php

namespace src\global\compositions;

final class GetHostInfo {
  static function is_localhost(): bool {
    $root_path = $_SERVER["DOCUMENT_ROOT"];
    foreach (GetDeveloperHomePaths::as_array() as $developer) {
      if (str_contains(haystack: $root_path, needle: $developer)) {
        return true;
      }
    }
    return false;
  }
}