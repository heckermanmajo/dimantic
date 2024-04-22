<?php

namespace src\global\components;

use src\core\Component;

readonly class AttentionBreadCrumbs extends Component {

  public function render(): void {
    ?>
    <div style="margin-top: 6px">
      <a href="/?p=a"> AttentionRootNode </a> <i class="fas fa-angle-double-right"></i>
      <a href="/?p=a"> AttentionSubNode </a> <i class="fas fa-angle-double-right"></i>
      <a href="/?p=a"> AttentionSubNode </a> <i class="fas fa-angle-double-right"></i>
    </div>
    <?php
  }

}