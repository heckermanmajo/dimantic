<?php

namespace cls;

/**
 * Class StringUtils
 *
 * A utility class for working with strings.
 */
class StringUtils {
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
  public static function get_title_from_md_content(string $content): string{
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
}

if (App::$run_inline_tests) {
  // Test 'test_get_title_from_md_content_with_valid_title'
  $markdownContent = "# This is the title\n This is the body";
  $actualTitle = StringUtils::get_title_from_md_content($markdownContent);
  if($actualTitle !== 'This is the title') {
    ob_clean();
    echo "\033[31m Test 'StringUtils::get_title_from_md_content' failed! Returned: $actualTitle 😞\n\033[0m";
    exit;
  }
  
  // Test 'test_get_title_from_md_content_with_no_title'
  $markdownContent = "";
  $actualTitle = StringUtils::get_title_from_md_content($markdownContent);
  if($actualTitle !== '[No Title]') {
    ob_clean();
    echo "\033[31m Test 'StringUtils::get_title_from_md_content' failed! Returned: $actualTitle 😞\n\033[0m";
    exit;
  }
}
