<?php

namespace cls;

class StringUtils {
  static function get_title_from_md_content(string $content){
    $lines = explode("\n", $content);
    $possibleHeaders = [
      "# " => [],
      "## " => [],
      "### " => [],
      "#### " => [],
      "##### " => [],
      "###### " => [],
    ];

    foreach ($lines as $line) {
      $line = trim($line);
      foreach ($possibleHeaders as $header => $value) {
        if (str_starts_with(haystack: $line, needle: $header)) {
          $title = str_replace(search: $header, replace: "", subject: $line);
          $possibleHeaders[$header][] = $title;
        }
      }
    }

    foreach ($possibleHeaders as $header => $value)
      if (count($value) > 0) return $value[0];

    return "[No Title]";
  }
}