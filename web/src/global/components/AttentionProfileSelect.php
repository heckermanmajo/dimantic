<?php

namespace src\global\components;

use src\core\Component;

readonly class AttentionProfileSelect extends Component {

  public function render(): void {
    ?>
    <select>
      <option>Attention Profile 1</option>
      <option>Attention Profile 2</option>
      <option>Attention Profile 3</option>
    </select>
    <?php
  }

}