<?php

namespace src\global\components\pages;

use src\core\Component;
use src\global\components\HomeSideBar;
use src\global\components\MainNavigationBar;
use src\global\components\View;

readonly class HomePage extends Component {

  public function render(): void {

    $main_nav_bar = new MainNavigationBar(
      middle: new readonly class extends Component{
        public function render(): void {
          ?>
          <a href="?p=home"> <i class="fas fa-mountain"></i> Overview </a> &nbsp; &nbsp; | &nbsp; &nbsp;
          <a href="?p=home&explore"> <i class="fas fa-binoculars"></i> Explore </a>
          <?php
        }
      },
      right: new readonly class extends Component {
        public function render(): void {
          ?>
          <a href="?p=account"> <i class="fas fa-user-cog"></i> Profile </a>
          <?php
        }
      }
    );
    $main_nav_bar->render();

    $sidebar = new HomeSideBar();
    ?>

    <div class="w3-row">

      <div class="w3-col l2 m2 s2">
        <?php $sidebar->render() ?>
      </div>

      <div class="w3-col l8 m8 s8">

        <h1>Home Page</h1>

        <p>Welcome to the home page.</p>

        <pre>

        Attention tasks: aufgaben die ich erledicgen kann um attention credoit zu verdienen
        -> arbitrations, etc.

        Attention Contracts: wo cih noch was erfüllen muss.

        Meine persönlichen unterhaltungen -> Dialog-Artige unterhaltungs-bäume, wie email aber besser
        Threaded unterhaltungen mit den papers, usw.

        Attention-Opportunities:
        -> where i can spend attention, new spaces
         intersting posts, etc.
        -> questions that map my emergent user profile

        Einen dialogue thread

        </pre>
      </div>

      <div class="w3-col l2 m2 s2">
        <div class="w3-card w3-padding w3-margin">

        </div>

      </div>

    </div>

    <?php
  }

}