<?php

namespace src\app\attention_tree\components;

use src\core\Component;

readonly class SpaceNodeJavaScriptComponent extends Component {

  public function render(): void {
    //  we need to render js for the space node component

    ?>
    <script>
        window.tree_function.space_node_component = {};
        window.tree_function.space_node_component.on_load = function (node, extra_data) {
          console.log("space node component loaded");
        }
    </script>
    <?php

  }

}