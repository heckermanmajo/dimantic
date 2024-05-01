<?php

namespace src\global\components;

use src\core\Component;

readonly class UserAttentionCreditBalance extends Component {

  public function render(): void {
    ?>
    <a href="?p=balance"> 124 <i style="color:green" class="fas fa-gem"></i> </a>
    <?php
  }
}