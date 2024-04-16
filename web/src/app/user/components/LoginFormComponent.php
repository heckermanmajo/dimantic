<?php

namespace src\app\user\components;

use src\app\user\requests\api\LoginRequest;
use src\core\Component;
use src\core\exceptions\NotLoggedIn;
use src\global\compositions\GetCurrentlyLoggedInAccount;
use src\global\compositions\GetLastRequestData;

readonly class LoginFormComponent extends Component {

  /**
   * @throws NotLoggedIn
   */
  public function render(): void {

    /*$proxy_request = new LoginRequest(
      [
        "space_id" => 12
      ],
      GetCurrentlyLoggedInAccount::get_account_or_crash(),
      is_proxy_request: true
    );

    if(!$proxy_request->is_allowed()){
      ?>
      <div>
        <h3>Not allowed</h3>
        <p>You are not allowed to send this form</p>
      </div>
      <?php
    }*/

    ?>

    <form
      class="w3-margin"
      style="padding: 2px;"
      action="api.php"
      method="post"
    >

      <?php LoginRequest::put_hidden_request_class_name_input_field() ?>

      <h5>Login: </h5>

      <label>
        Username<br>
        <input
          style="width: 100%; margin-bottom: 10px"
          type="text"
          id="username"
          name="username"
          value="<?= GetLastRequestData::get_last_post_field_by_name('username') ?>"
          required
        >
      </label>

      <br>

      <label>
        Password<br>
        <input style="width: 100%; margin-bottom: 10px" type="password" id="password" name="password" required>
      </label>

      <br><br>

      <button style="width: 100%">Login</button>

    </form>
    <?php
  }

}