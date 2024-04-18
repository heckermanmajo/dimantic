<?php

namespace src\global\components\pages;

use src\core\Component;
use src\global\components\MainNavigationBar;
use src\global\components\SpaceSidebar;

readonly class SpacePage extends Component {

  /**
   *
   * Question: how do we put stuff into a super space?
   * -> Simple: The delegates of the space just create posts.
   *
   *
   * NEWS:
   *
   * news are per space, this means, that a space creates a "log" of important events
   * and then there is a last_seen_news entry in each membership.
   *
   * -> This also allows for a "newspaper" mode, where only the most important news are shown
   *
   *
   */

  public function render(): void {
    $main_nav_bar = new MainNavigationBar(
      middle: (new readonly class extends Component {
        public function render(): void {
          ?>
          <a href="?p=space&news"> <i class="fas fa-newspaper"></i> News </a>&nbsp; &nbsp; | &nbsp; &nbsp;
          <a href="?p=space&shit"> <i class="fas fa-poo"></i> Shitboard </a> &nbsp;&nbsp;|&nbsp;&nbsp;
          <a href="?p=space&discussion"> <i class="fas fa-comments"></i> Discussion 🟠 </a> &nbsp;&nbsp;|&nbsp;&nbsp;
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

        <?php
        if (isset($_GET["news"])) {
          ?>
          <h2>News</h2>
          <p>View the news of the space</p>
          <?php
        } elseif (isset($_GET["shit"])) {
          ?>
          <h2>Shitboard</h2>
          <p>View the shitboard of the space</p>
          <?php
        } elseif (isset($_GET["discussion"])) {
          ?>
          <h1>Discussion</h1>
          <?php

        } elseif (isset($_GET["archive"])) {
          ?>
          <h1>Archive</h1>
          <?php
        } else {
          ?>
          <h1>Static</h1>
          <pre>
            - Projects, infos, space rules
          </pre>
          <?php
        }


        ?>


      </div>
    </div>
    <?php
  }

}