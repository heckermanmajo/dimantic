<?php

namespace cls\controller\command\always;

use cls\data\post\Post;

class YT implements \cls\Command {

  static function execute(Post $post, array $tokens, array &$not_executed_and_error_message_lines): void {
    $link = $tokens[1] ?? null;
    // extract the youtube id from the link
    $youtube_id = "";
    if($link !== null){
      $youtube_id = substr($link, strpos($link, "=") + 1);
    }
    if($link === null){
      $post->command_error_log .= "\n!err YT command failed; YT command misses link";
      return;
    }
    $html = <<<LINK
      <iframe 
        width="300" 
        height="200" 
        src="https://www.youtube.com/embed/$youtube_id" 
        title="Embeeded Video" 
        frameborder="0" 
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
        allowfullscreen></iframe>
LINK;
    $not_executed_and_error_message_lines[] = str_replace("\n", " ", $html);
  }

  static function get_command_name(): string {
    return "!yt";
  }
}