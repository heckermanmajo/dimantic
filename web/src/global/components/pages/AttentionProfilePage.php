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
          <a href="?p=ap"><i class="fas fa-eye"></i><i class="fas fa-project-diagram"></i> <i class="fas fa-mountain"></i> Overview </a> &nbsp; &nbsp; | &nbsp; &nbsp;
          <a href="?p=ap&board"> <i class="fas fa-object-group"></i> Whiteboard </a>  &nbsp; &nbsp; | &nbsp; &nbsp;
          <a href="?p=ap&explore"> <i class="fas fa-binoculars"></i> Explore </a>&nbsp; &nbsp; | &nbsp; &nbsp;
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
          <p>Configure your attention</p>
        <?php else: ?>
          <h1>Attention Overview</h1>
          <p>View your attention Overview</p>

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