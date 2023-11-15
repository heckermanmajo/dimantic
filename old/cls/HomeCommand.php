<?php

namespace cls;

interface HomeCommand {
  static function execute(array $tokens): void;
  static function get_command_name(): string;
}