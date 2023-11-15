<?php

namespace cls\controller\command\always;

use cls\data\post\Post;

class Err implements \cls\Command {

  static function execute(Post $post, array $tokens, array &$not_executed_and_error_message_lines): void {
    # error commands just does nothing -> this removes the error from before
  }

  static function get_command_name(): string {
    return "!err";
  }
}