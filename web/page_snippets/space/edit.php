<?php

use cls\App;
use cls\data\space\Space;
use cls\HtmlUtils;

/**
 * @throws Exception
 */
return function (Space $space, App $app): string {
  ob_start();

  echo HtmlUtils::get_markdown_editor_field_for_ajax(
    field_name: "content",
    ajax_end_point_path_from_root: "/request/space/edit_space_content/edit_space_content.php",
    init_text: $space->content,
    extra_json_fields: [
      "id" => $space->id,
    ]
  );

  ?>

  <h4> Delete Space </h4>

  <form method="post">
    <input type="hidden" name="action" value="delete_space">
    <input type="hidden" name="space_id" value="<?= $space->id ?>">
    <button style="color: red; border-color: red" class="sketch-button"> DELETE SPACE</button>
  </form>
  <?php

  return ob_get_clean();

};
