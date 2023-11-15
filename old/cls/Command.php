<?php

namespace cls;

use cls\data\post\Post;

interface Command {
  static function execute(Post $post, array $tokens, array &$not_executed_and_error_message_lines): void;
  static function get_command_name(): string;
}