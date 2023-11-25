<?php
declare(strict_types=1);

use cls\App;
use cls\data\account\Account;
use cls\data\account\NewsEntry;
use cls\data\dialoge\Dialogue;
use cls\HtmlUtils;
use cls\RequestError;

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
try {

  App::init_context(basename(__FILE__));
  $app = App::get();
  [$log, $warn, $err, $todo]
    = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  $app->handle_action_requests();

  HtmlUtils::head();

  ?>


  <?php
  if ($app->somebody_logged_in()) {
    ob_get_clean();
    header("Location: /home.php");
    exit;
  }
  else {
    ?>
    <div class="w3-row">
      <div class="w3-col m9 s9 l9">
        <div class="w3-card-4 w3-margin w3-padding">
          <h3>Dialoge</h3>

          <div class="w3-center">
            <img src="/res/dasein.png" width="300px">
          </div>
        </div>
      </div>
      <div class="w3-rest">

        <form
          class="w3-margin w3-padding w3-card-4"
          method="post">
          <input type="hidden" name="action" value="login">
          <h4>Login</h4>
          <?= ($app->executed_action == "login") ?: $app->action_error?->get_error_card() ?>
          <label>
            <span><small>Username/Email</small></span>
            <br>
            <input type="text" name="username_or_email" value="">
          </label><br><br>
          <label>
            <span><small>Password</small></span>
            <br>
            <input type="password" name="password" value="">
          </label><br><br>
          <button class="button" type="submit">Login</button>
        </form>

        <form class="w3-margin w3-padding w3-card-4" method="post">
          <h4>Register</h4>
          <?= ($app->executed_action == "register") ?: $app->action_error?->get_error_card() ?>
          <input type="hidden" name="action" value="register">
          <label>
            <span><small>Username</small></span>
            <br>
            <input type="text" name="username" value="">
          </label><br><br>
          <label>
            <span><small>Email</small></span>
            <br>
            <input type="text" name="email" value="">
          </label><br><br>
          <label>
            <span><small>Password</small></span>
            <br>
            <input type="password" name="password" value="">
          </label><br><br>
          <button class="button" type="submit">Register</button>
        </form>
      </div>
    </div>
    <?php
  }

  HtmlUtils::footer($app);
}
catch (\Throwable $e) {
  App::dump_logs(t: $e);
}