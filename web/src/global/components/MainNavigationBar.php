<?php

namespace src\global\components;

use src\core\Component;

readonly class MainNavigationBar extends Component {

  function __construct(
    private readonly ?Component $middle = null,
    private readonly ?Component $right = null,
  ) {
  }

  public function render(): void {

    ?>
    <div
      style="height: 35px"
      class="w3-card w3-center">

      <div class="w3-left">

        <?php (new AttentionProfileSelect())->render() ?>

        <a href="?p=balance"> 124 <i style="color:green" class="fas fa-gem"></i> </a>

      </div>

      <?php $this->middle?->render() ?>

      <div class="w3-right">
        <?php $this->right?->render() ?>
      </div>

    </div>
    <?php

  }

}