<?php

namespace src\global\compositions;

final class GetDeveloperHomePaths {
  /**
   * @return array<string>
   */
  static function as_array(): array {
    return [
      "/home/majo/",
      # ...
      # add your home path here if you are a developer ...
    ];
  }
}