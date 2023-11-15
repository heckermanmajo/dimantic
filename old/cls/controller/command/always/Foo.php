<?php

namespace cls\controller\command\always;

use cls\data\post\Post;

class Foo implements \cls\Command {

  static function execute(Post $post, array $tokens, array &$not_executed_and_error_message_lines): void {
    $not_executed_and_error_message_lines[] = "Foo was executed.";
  }

  static function get_command_name(): string {
    return "!foo";
  }
}