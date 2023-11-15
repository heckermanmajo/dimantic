<?php

namespace cls\controller\command\once;

use cls\data\post\Post;

class Bar implements \cls\Command {

  static function execute(Post $post, array $tokens, array &$not_executed_and_error_message_lines): void {
    $not_executed_and_error_message_lines[] = "Bar was executed.";
    #$post->command_error_log .= "\n!!Bar command failed";
    $post->command_feedback_log .= "\n!!Bar command worked";
  }

  static function get_command_name(): string {
    return "!bar";
  }
}