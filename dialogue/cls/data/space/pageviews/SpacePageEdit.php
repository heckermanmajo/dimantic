<?php

namespace cls\data\space\pageviews;

use cls\App;
use cls\data\space\Space;
use cls\HtmlUtils;

class SpacePageEdit {
  static function display(Space $space, App $app): string {
    ob_start();
    echo HtmlUtils::get_markdown_editor_field_for_ajax(
      field_name: "content",
      ajax_end_point_path_from_root: "/request/space/edit_space_content/edit_space_content.php",
      init_text: $space->content,
      extra_json_fields: [
        "id" => $space->id,
      ]
    );

    return ob_get_clean();
  }
}