<?php

namespace src\app\attention_tree\components;

use src\app\attention_tree\data\dataclass\JSTreeNode;
use src\app\attention_tree\data\tables\AttentionNode;
use src\app\attention_tree\request\api\LoadSpaceNodeChildren;
use src\core\Component;

readonly class SpaceNodeJsonComponent extends Component {
  function __construct(
    private AttentionNode $node
  ) {
  }

  public function render(): void {
    //  we need to render json

    $request_id = LoadSpaceNodeChildren::encrypt_class_name();

    $tree_node = new JSTreeNode(
      "Space",
      "fas fa-space-shuttle",
      [],
      "api.php?__request_id=$request_id&id={$this->node->id}"
    );

    echo $tree_node->get_json();

  }

}