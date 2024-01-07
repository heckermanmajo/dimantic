<?php
declare(strict_types=1);

use cls\App;
use cls\HtmlUtils;


require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
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
      <div class="w3-col m8 s8 l8">
        <div class=w3-margin w3-padding">
          <h1 class="w3-center"> Dimantic </h1>
          <h3 class="w3-center"> The Communication Game </h3>
<!-- Social-Networks Solved-->
          <div class="w3-center">
            <img src="/res/diamant.svg" width="300px">
          </div>

          <p>
            Make Social networks great again!
          </p>

          <p>
            Hier die Mission.
          </p>

        </div>
      </div>
      <div class="w3-rest">

        <form
          class="w3-margin w3-padding sketch-card "
          method="post">
          <input type="hidden" name="action" value="login">
          <input type="hidden" name="allow_resend_of_request" value="true">
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
          <button class="sketch-button" type="submit">Login</button>
        </form>

        <form class="w3-margin w3-padding sketch-card " method="post">
          <h4>Register</h4>
          <?= ($app->executed_action == "register") ?: $app->action_error?->get_error_card() ?>
          <input type="hidden" name="action" value="register">
          <input type="hidden" name="allow_resend_of_request" value="true">
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
          <button class="sketch-button" type="submit">Register</button>
        </form>
      </div>
    </div>
    <?php
  }

  HtmlUtils::footer();
}
catch (\Throwable $e) {
  App::dump_logs(t: $e);
}