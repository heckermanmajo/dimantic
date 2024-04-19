<?php

namespace src\global\components\pages;

use src\core\Component;
use src\global\components\AttentionProfileSidebar;
use src\global\components\MainNavigationBar;

readonly class AttentionProfilePage extends Component {

  public function render(): void {
    $main_nav_bar = new MainNavigationBar(
      middle: new readonly class extends Component {
        public function render(): void {
          ?>
          <a href="?p=ap&feed"> <i class="fas fa-bars"></i> Feed </a> &nbsp; &nbsp; | &nbsp; &nbsp;
          <a href="?p=ap&favs"> <i class="fas fa-star"></i> Favs </a>  &nbsp; &nbsp; | &nbsp; &nbsp;
          <a href="?p=ap&history"> <i class="fas fa-user-clock"></i>History </a>
          <?php
        }
      },
      right: new readonly class extends Component {
        public function render(): void {
          ?>
          <a href="?p=ap&config"> <i class="fas fa-filter"></i> Configuration </a>
          <?php
        }
      }
    );
    $main_nav_bar->render();

    $space_sidebar = new AttentionProfileSidebar();
    ?>
    <div class="w3-row">

      <div class="w3-col m2 l2 s2">

        <?php $space_sidebar->render() ?>

      </div>

      <div class="w3-rest">

        <?php if (isset($_GET["config"])): ?>
          <h1>Attention CONFIG</h1>
          <p>Configure your attention feed</p>
        <?php else: ?>
          <h1>Attention FEED</h1>
          <p>View your attention feed</p>

          <pre>

            News are per space

            -> all attention drops
            -> all subscriber content

        </pre>


        <?php endif; ?>

      </div>

    </div>
    <?php
  }


}