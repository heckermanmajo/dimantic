<?php
namespace cls\data\space;

use cls\StringUtils;

/**
 * An attention board has always 3 times on the x-axis: short, mid, long term.
 * But it can create its own categories.
 *
 * This is done by the space-konsul.
 */
class AttentionBoardCategory extends \cls\DataClass {
  
  /**
   * Markdown description of the category.
   * The name of the category is the extracted title
   * from the markdown description.
   *
   * @see StringUtils::get_title_from_md_content()
   */
  var string $description = "";
}