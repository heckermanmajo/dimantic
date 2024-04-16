<?php

namespace src\global\compositions;

final class GetPath {
  static function get_home_path(): string {
    foreach (GetDeveloperHomePaths::as_array() as $developer) {
      if (str_contains(haystack: $_SERVER["DOCUMENT_ROOT"], needle: $developer)) {
        return $developer;
      }
    }
    return "";
  }

}