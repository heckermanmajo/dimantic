<?php

namespace src\global\components;

use src\core\Component;

class MainNavigationBar extends Component {

  function __construct(
    private readonly ?Component $middle = null,
    private readonly ?Component $right = null,
  ){}

  public function render(): void {

    ?>
    <nav class="w3-card">

      <div class="w3-left">
        <?php (new AttentionProfileSelect())->render() ?>
      </div>

      <div class="w3-center">
        <?php $this->middle?->render() ?>
      </div>

      <div class="w3-right">
        <?php $this->right?->render() ?>
      </div>

    </nav>
    <?php

  }

}