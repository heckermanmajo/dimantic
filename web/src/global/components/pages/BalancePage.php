<?php

namespace src\global\components\pages;

use src\core\Component;
use src\global\components\MainNavigationBar;
use src\global\components\View;

readonly class BalancePage extends Component{

  public function render(): void {
    $main_nav_bar = new MainNavigationBar(
      middle: new View("HOME"),
      right: new View("RIGHT")
    );
    $main_nav_bar->render();
    ?>
    <h1> Balance page: view on what you pay out and pay in </h1>
    <?php
  }

}