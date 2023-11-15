<?php
declare(strict_types=1);
namespace cls;

class Interpreter {
  static function parse(string $message, array $options = []): string {
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
    $message = htmlspecialchars($message);
    $message = str_replace("\n", "<br>", $message);
    $message = str_replace("  ", "&nbsp;&nbsp;", $message);
    # check umlaute
    $message = str_replace("ä", "&auml;", $message);
    $message = str_replace("ö", "&ouml;", $message);
    $message = str_replace("ü", "&uuml;", $message);
    $message = str_replace("Ä", "&Auml;", $message);
    $message = str_replace("Ö", "&Ouml;", $message);
    $message = str_replace("Ü", "&Uuml;", $message);
    $message = str_replace("ß", "&szlig;", $message);
    return $message;
  }
  #static function parse_edit_commands(){
  #  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  #}

  static function extract_title(){
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  }
  static function extract_short_content(){
    [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  }
}