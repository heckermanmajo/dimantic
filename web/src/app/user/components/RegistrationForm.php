<?php

namespace src\app\user\components;

use src\app\user\requests\api\RegistrationRequest;
use src\core\Component;
use src\global\compositions\GetLastRequestData;

readonly class RegistrationForm extends Component {

  public function render(): void {

    if(GetLastRequestData::get_last_request() instanceof RegistrationRequest) {
        if(GetLastRequestData::get_last_request()->is_done()){
          echo "<div class='w3-panel w3-green w3-padding w3-margin'>Account created</div>";
        }
        else{
          GetLastRequestData::get_error_card_of_fast_request()?->render();
        }
    }


    ?>
    <form
      class="w3-margin"
      style="padding: 2px;"
      action="api.php"
      method="post">

      <?php RegistrationRequest::put_hidden_request_class_name_input_field(); ?>

      <h5>Register: </h5>

      <label>
        Username<br>
        <input
          style="width: 100%; margin-bottom: 10px"
          type="text"
          name="username"
          value="<?= GetLastRequestData::get_last_post_field_by_name("username")?>"
          required
        >
      </label>

      <br>

      <label>
        Password<br>
        <input style="width: 100%; margin-bottom: 10px" type="password" name="password" required>
      </label>

      <br>

      <label>
        Password Repeat<br>
        <input style="width: 100%; margin-bottom: 10px" type="password" name="password_repeat" required>
      </label>

      <br>

      <label>
        Email<br>
        <input
          style="width: 100%; margin-bottom: 10px"
          type="email"
          name="email"
          value="<?= GetLastRequestData::get_last_post_field_by_name("email")?>"
          required
        >
      </label>

      <br>

      <label>
        <input style=" margin-bottom: 10px" type="checkbox" name="privacy_policy" required>
        Read <span
          style="color: #3f51b5;text-decoration: underline; text-decoration-color: #3f51b5; cursor:pointer;"
          onclick="showOverlay('/cls/data/account/wiki/privacy_policy.php')"
        > Privacy Policy</span>
      </label>

      <br>

      <label>
        <input style=" margin-bottom: 10px" type="checkbox" name="agb" required>
        Read <span
          style="color: #3f51b5;text-decoration: underline; text-decoration-color: #3f51b5; cursor:pointer;"
          onclick="showOverlay('/cls/data/account/wiki/agb.php')"
        > General terms and conditions </span>
      </label>

      <br>
      <br>

      <button style="width: 100%">Register</button>

    </form>
    <?php
  }
}