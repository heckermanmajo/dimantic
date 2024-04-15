<?php

namespace src\core;

use src\global\compositions\GetEnvironmentMode;

final class L {

  static array $debug_logs = [];

  private static function log(string $message, array $data = [], string $type = ""): void {

    $prefix = "";
    if(GetEnvironmentMode::is_debug()) {
      $stack = debug_backtrace();
      $line = $stack[0]["line"];
      $prefix = $stack[1]["function"] . " " . $line . " ";

    }

    self::$debug_logs[] = "$type $prefix: $message";
    self::$debug_logs[] = json_encode($data, JSON_PRETTY_PRINT);

  }

  static function info(string $message, array $data = [], string $func = ""): void {
    # get the line number from the call stack
    self::log($func . $message, $data, "[INFO]");
  }

  static function warn(string $message, array $data = [], string $func = ""): void {
    self::log($func . $message, $data, "[WARN]");
  }

  static function err(string $message, array $data = [], string $func = ""): void {
    self::log($func . $message, $data, "[ERR]");
  }
}