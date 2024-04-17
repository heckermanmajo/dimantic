<?php

namespace src\global\components\pages;

use src\core\Component;
use src\global\components\HomeSideBar;
use src\global\components\MainNavigationBar;
use src\global\components\View;

readonly class HomePage extends Component {

  public function render(): void {

    $main_nav_bar = new MainNavigationBar(
      middle: new View("HOME"),
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
          List of dialogues<br>
          <hr>
          <span style="color: #ff00ce"> NEWLY STARTED DIALOGUES: </span><br>
          <i class="fas fa-people-arrows"></i> Dialogue 2<br><br>
          <i class="fas fa-people-arrows"></i> Dialogue 3<br><br>
          <hr>
          <span style="color: #ff6a00"> NEW Threads: </span><br>
          <i class="fas fa-people-arrows"></i> Dialogue 2<br><br>
          <i class="fas fa-people-arrows"></i> Dialogue 3<br><br>
          <hr>
          <span style="color: red"> NEW MESSAGES: </span><br>
          <i class="fas fa-people-arrows"></i> Dialogue 2<br><br>
          <i class="fas fa-people-arrows"></i> Dialogue 3<br><br>
          <hr>
          <br>
          <i class="fas fa-people-arrows"></i> Dialogue 1<br><br>
          <i class="fas fa-people-arrows"></i> Dialogue 2<br><br>
          <i class="fas fa-people-arrows"></i> Dialogue 3<br><br>
        </div>

      </div>

    </div>

    <?php
  }

}