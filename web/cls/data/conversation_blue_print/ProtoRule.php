<?php

namespace cls\data\conversation_blue_print;

use cls\App;
use cls\DataClass;
use cls\HtmlUtils;
use Exception;

/**
 * Simple conversation rule for a conversation blueprint.
 * - since it is a blueprint, it is not a conversation yet.
 */
class ProtoRule extends DataClass {
  var int $author_id = 0;
  var int $blue_print_id = 0;
  var string $content = "";

  /**
   * @throws Exception
   */
  function given_user_can_edit_proto_rule(): bool {
    $app = App::get();
    return $app->get_currently_logged_in_account()->id === $this->author_id;
  }

  /**
   * @throws Exception
   */
  function get_card(): string {
    ob_start();
    $app = App::get();
    # todo: add numbers to the rules ...
    # todo: add edit button
    ?>
    <div class="sketch-card w3-margin w3-padding">

      <div id="proto_rule_content_<?= $this->id ?>">
        <?= $app->markdown_to_html($this->content) ?>
      </div>

      <?php if ($this->given_user_can_edit_proto_rule()): ?>

        <button
          class="sketch-button"
          onclick="FN_TOGGLE('edit_proto_rule_<?= $this->id ?>')"
        > Edit Protorule
        </button>

        <div style="display: none" id="edit_proto_rule_<?= $this->id ?>">

          <?= HtmlUtils::get_markdown_editor_field_for_ajax(

            field_name: "content",

            ajax_end_point_path_from_root: "/request/blueprint/edit_proto_rule/edit_proto_rule.php",

            init_text: $this->content,

            extra_json_fields: [
              "proto_rule_id" => $this->id,
            ],
            onchange_js_code: /** @lang JavaScript */ "

               fetch(
                '/request/blueprint/get_proto_rule_card_html/get_proto_rule_card_html.php',
                {
                  method: 'POST',
                  mode: 'no-cors',
                  headers: {
                    'Content-Type': 'application/json',
                  },
                  body: form_data,
                })
                .then(response => response.json())
                .then(data => {
                  document.getElementById('proto_rule_content_{$this->id}').innerHTML = data.data;
                })
                .catch((error) => {
                  console.error('Error:', error);
                });
            "
          ) ?> <!-- end of markdown editor -->

        </div>

        <button onclick="FN_TOGGLE('edit_proto_rule_<?= $this->id ?>')"> Close </button>
      <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
  }
}