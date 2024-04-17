<?php

namespace src\global\components\pages;

use src\core\Component;
use src\global\components\MainNavigationBar;
use src\global\components\SpaceSidebar;

readonly class SpacePage extends Component{

  /**
   *
   * Question: how do we put stuff into a super space?
   * -> Simple: The delegates of the space just create posts.
   *
   *
   */

  public function render(): void {
    $main_nav_bar = new MainNavigationBar(
      middle: (new readonly class extends Component {
        public function render(): void {
          ?>
          <a href="?p=space&home"> <i class="fas fa-poo"></i> Shitboard </a> &nbsp;&nbsp;|&nbsp;&nbsp;
          <a href="?p=space&explore"> <i class="fas fa-comments"></i> Discussion </a> &nbsp;&nbsp;|&nbsp;&nbsp;
          <a href="?p=space&static"> <i class="fas fa-landmark"></i> Static </a> &nbsp;&nbsp;|&nbsp;&nbsp;
          <a href="?p=space&archive"><i class="fas fa-archive"></i> Archive </a>
          <?php
        }
      }),
      right: new readonly class extends Component {
        public function render(): void {
          # space settings -->
          ?>
          <a href="?p=settings&id=123"> <i class="fas fa-tools"></i> Settings </a>
          <?php
        }
      }
    );
    $main_nav_bar->render();

    $side_bar = new SpaceSidebar();

    ?>
    <div class="w3-row">
      <div class="w3-col " style="width: 350px">
        <?php $side_bar->render() ?>
      </div>
      <div class="w3-rest">
        <h1>Space Page </h1>
      </div>
    </div>
    <?php
  }

}