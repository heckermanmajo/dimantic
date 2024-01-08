<?php

namespace cls;

/**
 * Class StringUtils
 *
 * A utility class for working with strings.
 */
class MarkdownUtils {
  /**
   * Array of possible header prefixes for markdown headers.
   *
   * @var string[]
   */
  private const POSSIBLE_HEADERS = ["# ", "## ", "### ", "#### ", "##### ", "###### "];

  /**
   * Retrieves the title from the given Markdown content.
   *
   * @param string $content The Markdown content.
   *
   * @return string The retrieved title from the Markdown content.
   *
   */
  public static function get_title_from_md_content(string $content): string {
    [$headerMap, $first_non_empty_line] = self::process_lines($content);

    foreach ($headerMap as $header => $value)
      if (count($value) > 0) return $value[0];

    return empty($first_non_empty_line) ? "[No Title]" : $first_non_empty_line;
  }

  /**
   * Retrieves the Markdown content without the title.
   *
   * @param string $content The Markdown content with titles.
   *
   * @return string The Markdown content without the title.
   */
  public static function get_md_content_without_title(string $content): string {
    [$headerMap,] = self::process_lines($content);

    foreach ($headerMap as $header => $value)
      if (count($value) > 0) {
        $title = $value[0];
        $content = str_replace(search: $header . $title, replace: "", subject: $content);
      }

    return $content;
  }

  /**
   * Processes the lines of a given content and extracts header information.
   *
   * @param string $content The content to process.
   * @return array An array containing the header map and the first non-empty line.
   */
  private static function process_lines(string $content): array {
    $lines = explode("\n", $content);
    $first_non_empty_line = "";
    $headerMap = array_fill_keys(self::POSSIBLE_HEADERS, []);

    foreach ($lines as $line) {
      $line = trim($line);
      foreach ($headerMap as $header => $value) {
        if (empty($line)) continue;
        $first_non_empty_line ??= $line;
        if (!str_starts_with(haystack: $line, needle: $header)) continue;
        $title = str_replace(search: $header, replace: "", subject: $line);
        $headerMap[$header][] = $title;
      }
    }

    return [$headerMap, $first_non_empty_line];
  }

  /**
   * @param string $markdown_text
   * @return void
   */
  static function get_short_desc_from_start(
    string $markdown_text,
    bool   $ignore_header,
    int    $content_lines = 3
  ): string {
    $lines = explode(separator: "\n", string: $markdown_text);
    $counter = 0;
    $short_desc = [];
    foreach ($lines as $line) {
      if ($counter > $content_lines) break;
      $line = trim(string: $line);
      if (empty($line)) continue;
      if ($ignore_header && str_starts_with(haystack: $line, needle: "#")) continue;
      $short_desc [] = $line;
      $counter++;
    }
    $short_desc = implode(separator:"\n", array:$short_desc);
    # allow max 120 chars
    if (strlen(string: $short_desc) > 120) {
      $short_desc = substr(string: $short_desc, offset: 0, length: 120) . "...";
    }
    if (empty($short_desc)) {
      $short_desc = "[No Description]";
    }
    return $short_desc;

  }

}

if (App::$run_inline_tests) {
  // Test 'test_get_title_from_md_content_with_valid_title'
  $markdownContent = "# This is the title\n This is the body";
  $actualTitle = MarkdownUtils::get_title_from_md_content($markdownContent);
  if ($actualTitle !== 'This is the title') {
    ob_clean();
    echo "\033[31m Test 'StringUtils::get_title_from_md_content' failed! Returned: $actualTitle 😞\n\033[0m";
    exit;
  }

  // Test 'test_get_title_from_md_content_with_no_title'
  $markdownContent = "";
  $actualTitle = MarkdownUtils::get_title_from_md_content($markdownContent);
  if ($actualTitle !== '[No Title]') {
    ob_clean();
    echo "\033[31m Test 'StringUtils::get_title_from_md_content' failed! Returned: $actualTitle 😞\n\033[0m";
    exit;
  }
}
