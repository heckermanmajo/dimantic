<?php

namespace src\global\components\pages;

use src\app\user\components\LoginFormComponent;
use src\app\user\components\RegistrationForm;use src\core\Component;
use src\core\exceptions\NotLoggedIn;

readonly class LandingPageOffline extends Component {
  /**
   * @throws NotLoggedIn
   */
  public function render(): void {

    $login_form = new LoginFormComponent();
    $reg_form = new RegistrationForm();

    ?>

    <div class="w3-row w3-margin">

      <div class="w3-col s9 m9 l9">
        <h2> Welcome to Dimantic </h2>
        <p> Please login or register to continue </p>
      </div>

      <div class="w3-rest">

        <?php $login_form->render(); ?>

        <hr>

        <?php $reg_form->render(); ?>

      </div>

    </div>



    <!--<button onclick="showOverlay('index.php')"> SHOW INDEX IN OVERLAY</button>-->
    <?php
  }
}