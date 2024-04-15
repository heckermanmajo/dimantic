<?php

namespace src\global\compositions;

final class GetEnvironmentMode {
  static function is_debug(): bool {
    return true; # for alpha ....
    # cli mode is always debug mode
    #if (FN_IS_CLI()) {
    #  return true;
    #}

    #$root_path = $_SERVER["DOCUMENT_ROOT"];
    #foreach (DEVELOPERS_HOME_PATHS as $developer) {
    #  if (str_contains(haystack: $root_path, needle: $developer)) {
    #    return true;
    #  }
    #}
    #return false;
  }
}