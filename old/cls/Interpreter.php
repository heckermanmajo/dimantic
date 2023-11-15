<?php

namespace cls;

use App;
use cls\data\idea_space\IdeaSpace;
use cls\data\league\AttentionDimension;
use cls\data\league\AttentionLeague;
use cls\data\post\Post;
use Exception;

class Interpreter {

  /** @return array<string, callable> the commands that are executed only once when a post is created or updated. */
  private static function get_execute_once_commands(): array {
    $path = $_SERVER["DOCUMENT_ROOT"] . "/cls/controller/command/once";
    foreach (scandir($path) as $file) {
      if (str_ends_with($file, ".php")) {
        $class_name = "cls\\controller\\command\\once\\" . str_replace(".php", "", $file);
        $commands[$class_name::get_command_name()] = $class_name::execute(...);
      }
    }
    return $commands;
  }

  /** @return array<string, callable>  Returns the commands that are executed every time a post is displayed. */
  private static function get_execute_always_commands(): array {
    $path = $_SERVER["DOCUMENT_ROOT"] . "/cls/controller/command/always";
    foreach (scandir($path) as $file) {
      if (str_ends_with($file, ".php")) {
        $class_name = "cls\\controller\\command\\always\\" . str_replace(".php", "", $file);
        $commands[$class_name::get_command_name()] = $class_name::execute(...);
      }
    }
    return $commands;
  }

  /**
   * Parse a line into tokens.
   * Currently only supports strings and spaces.
   * Spaces delimit tokens.
   *
   * @return array<string>
   * @throws Exception
   */
  private static function get_tokens_of_line(string $line): array {
    $line = trim($line);
    $chars = str_split($line);
    $tokens = [];
    $current_token = "";
    $is_in_quotes = false;

    for ($i = 0; $i < count($chars); $i++) {
      $char = $chars[$i];
      switch ($char) {

        case "\\":
          $current_token .= $char;
          $i++;
          $current_token .= $chars[$i];
          break;

        case "\"":
          $is_in_quotes = !$is_in_quotes;
          #$current_token .= $char;
          if (!$is_in_quotes) {
            $tokens[] = $current_token;
            $current_token = "";
          }
          break;

        case " ":
          if ($is_in_quotes) {
            if($current_token !== "\!"){
              $current_token .= $char;
            }
          }
          else {
            if ($current_token !== "") {
              $tokens[] = $current_token;
              $current_token = "";
            }
          }
          break;

        default:
          $current_token .= $char;
          break;
      } # end switch
    } # end for

    if ($current_token !== "") {
      $tokens[] = $current_token;
    }

    foreach ($tokens as $token) {
      if (!str_starts_with($token, "\"") && str_ends_with($token, "\"")) {
        throw new Exception("!!Invalid token: $token, missing starting quote.");
      }
      if (str_starts_with($token, "\"") && !str_ends_with($token, "\"")) {
        throw new Exception("!!Invalid token: $token, missing ending quote.");
      }
    }

    return $tokens;
  }

  /**
   * This function is called if a post is updated or created.
   * It executes all commands that are only executed once.
   * Then it saves the result content of the execution of the command
   * as new post content.
   *
   * @param Post $post the post where the once commands should be executed - its content will be changed
   * @return void
   */
  static function execute_once_commands(Post &$post): void {
    $not_executed_and_error_message_lines = []; # returned at the end, joined by \n
    $lines = explode("\n", $post->content);
    $_commands = [];
    foreach (self::get_execute_once_commands() as $command_name => $command) {
      $_commands[strtoupper($command_name)] = $command;
    }
    $commands = $_commands;

    foreach ($lines as $possible_command_line) {
      if (trim($possible_command_line) == "") {
        $not_executed_and_error_message_lines[] = "";
        continue;
      }
      try {
        $tokens = self::get_tokens_of_line($possible_command_line);
        $command = strtoupper($tokens[0]); # we want to ignore case!
        if (
          str_starts_with($command, "!")
          && in_array($command, array_keys($commands))
        ) {
          # once commands can handle their errors themselves
          # they write into post->command_error_log and post->command_feedback_log
          $commands[strtoupper($command)](
            $post,
            $tokens,
            $not_executed_and_error_message_lines
          );
        }
        # no command or display command or settings command
        else $not_executed_and_error_message_lines[] = $possible_command_line;

      }
        # this happens on syntax error
      catch (Exception $e) {
        $not_executed_and_error_message_lines[] = "!err " . $e->getMessage();
        $not_executed_and_error_message_lines[] = $possible_command_line;
      }
    }
    $new_content = implode("\n", $not_executed_and_error_message_lines);
    $post->content = $new_content;
    $post->save(App::get_connection());
  }

  /**
   * The commands that are executed every time a post is displayed.
   *
   * @param Post $post the post where the once commands should be executed - its content will not be changed
   * @return string the html save content of the post after applying the always-commands
   */
  static function execute_always_commands(Post|IdeaSpace|AttentionLeague|AttentionDimension $post): string {

    if(!($post instanceof Post)){
      throw new Exception("!!Post expected, others not implemented yet.");
    }
    $old_content = $post->content;  # small hack ...
    $post->content = htmlspecialchars($post->content);
    $not_executed_and_error_message_lines = []; # returned at the end, joined by \n
    $lines = explode("\n", $post->content);
    $_commands = [];
    foreach (self::get_execute_always_commands() as $command_name => $command) {
      $_commands[strtoupper($command_name)] = $command;
    }
    $commands = $_commands;

    $failed_display_commands = [];
    foreach ($lines as $possible_command_line) {
      if (trim($possible_command_line) == "") {
        $not_executed_and_error_message_lines[] = "";
        continue;
      }
      try {
        $tokens = self::get_tokens_of_line($possible_command_line);
        $command = strtoupper($tokens[0]); # we want to ignore case!
        if (
          str_starts_with($command, "!")
          && in_array($command, array_keys($commands))
        ) {
            try {
              $commands[strtoupper($command)](
                $post,
                $tokens,
                $not_executed_and_error_message_lines
              );
            }catch (\Throwable $t){
              $failed_display_commands[] = "At your post ... the following commands failed:";
              $failed_display_commands[] = $t->getMessage();
              $failed_display_commands[] = $possible_command_line;
            }
        }
        # no command or display command or settings command
        else {
          # todo: what about normal ! use in the text????
          if (str_starts_with($command, "!")) continue; # if comment or not executed command ...
          $not_executed_and_error_message_lines[] = $possible_command_line;
        }
      }
      # this happens on syntax error
      catch (Exception $e) {
        $not_executed_and_error_message_lines[] = $e->getMessage();
        $not_executed_and_error_message_lines[] = "!!" . $possible_command_line;
      }
    } // end foreach

    if (count($failed_display_commands) > 0) {
      # todo: send error notification to author of post
      #       but only if no other error notification was created for this post
    }

    $content = implode("\n", $not_executed_and_error_message_lines);
    $content = self::execute_text_style_commands($content); # space 0> &nbsp;
    $post->content = $old_content;
    return $content;
  }

  static function execute_home_commands(string $home_commands): void {
    # has context via App class & $_SESSION
    $lines = explode("\n", $home_commands);
    $_commands = [];
    foreach (self::get_execute_home_commands() as $command_name => $command) {
      $_commands[strtoupper($command_name)] = $command;
    }
    $commands = $_commands;

    foreach ($lines as $possible_command_line) {
      if (trim($possible_command_line) == "") continue;
      try {
        $tokens = self::get_tokens_of_line($possible_command_line);
        $command = strtoupper($tokens[0]); # we want to ignore case!
        if (
          str_starts_with($command, "!")
          && in_array($command, array_keys($commands))
        ) {
          $commands[strtoupper($command)]($tokens);
        }
        # no command or display command or settings command
        else App::$command_result_logs[] = "Command not known: " . $possible_command_line;

      }
        # this happens on syntax error
      catch (Exception $e) {
        #todo where to write the error message?
      }
    }
  }

  /**
   * Convert simple style commands to html code - called by execute_always_commands
   * @param string $content
   * @return string
   */
  private static function execute_text_style_commands(string $content): string {
    $content = str_replace("\n", "<br>", $content);
    # todo
    # qbgfkwrgbwre !red(wergw) aergf reg -> <span style="color: red">wergw</span>
    return $content;
  }


  #########################################
  # Tests
  #########################################

  static function test_get_tokens_of_line() {
    $line = "a b c";
    $tokens = self::get_tokens_of_line($line);
    assert($tokens == ["a", "b", "c"], print_r($tokens, true));

    $line = "a \"b c\"";
    $tokens = self::get_tokens_of_line($line);
    assert($tokens == ["a", "b c"], print_r($tokens, true));

    $line = "\"a b\" c";
    $tokens = self::get_tokens_of_line($line);
    assert($tokens == ["a b", "c"], print_r($tokens, true));

    $line = "\"a b\" \"c\"";
    $tokens = self::get_tokens_of_line($line);
    assert($tokens == ["a b", "c"], print_r($tokens, true));

    $line = "\"a b\" \"c d\"";
    $tokens = self::get_tokens_of_line($line);
    assert($tokens == ["a b", "c d"]);

    $line = "\"a b\" \"c d\" e";
    $tokens = self::get_tokens_of_line($line);
    assert($tokens == ["a b", "c d", "e"]);

    $line = "\"a b\" \"c d\" e \"f\\\" g\"";
    $tokens = self::get_tokens_of_line($line);
    assert($tokens == ["a b", "c d", "e", "f\\\" g"], print_r($tokens, true));

  }

}

