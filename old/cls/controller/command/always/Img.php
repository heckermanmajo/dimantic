<?php

namespace cls\controller\command\always;

use cls\data\post\Post;

class Img implements \cls\Command {

  static function execute(Post $post, array $tokens, array &$not_executed_and_error_message_lines): void {
    $link = $tokens[1] ?? null;
    $html = <<<LINK
      <img src="$link" width="500px">
LINK;
    $not_executed_and_error_message_lines[] = str_replace("\n", " ", $html);
  }

  static function get_command_name(): string {
    return "!img";
  }
}