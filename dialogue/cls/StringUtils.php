<?php

namespace cls;

class StringUtils {
  static function get_title_from_md_content(string $content){
    $lines = explode("\n", $content);
    $first_non_empty_line = "";
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
        if (strlen($line) == 0) continue;
        if (strlen($first_non_empty_line) == 0) $first_non_empty_line = $line;
        if (str_starts_with(haystack: $line, needle: $header)) {
          $title = str_replace(search: $header, replace: "", subject: $line);
          $possibleHeaders[$header][] = $title;
        }
      }
    }

    foreach ($possibleHeaders as $header => $value)
      if (count($value) > 0) return $value[0];

    if (strlen($first_non_empty_line) > 0) return $first_non_empty_line;

    return "[No Title]";
  }
}