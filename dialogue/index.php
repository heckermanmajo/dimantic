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
  $app->init_database();
  [$log, $warn, $err, $todo]
    = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);


  switch ($_POST["action"] ?? "") {


    case "register":
      $log("register new account");
      $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/account/register/register.php"))(
        $app, $_POST
      );
      if ($result instanceof RequestError) {
        $err("register error: " . $result->dev_message);
        $register_error = $result; # used in the bottom registration form
      }
      else {
        # pass since all user dependent data is used beneath
      }
      break;


    case "login":
      $log("login");
      $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/account/login/login.php"))(
        $app, $_POST
      );
      if ($result instanceof RequestError) {
        $err("login error: " . $result->dev_message);
        $login_error = $result;
      }
      else {
        # pass since all user dependent data is used beneath
      }
      break;


    case "activate_dialogue":
      $log("activate dialogue");
      $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/dialogue/start_dialogue/start_dialogue.php"))(
        $app, $_POST
      );
      if ($result instanceof RequestError) {
        $err("activate dialogue error: " . $result->dev_message);
        $activate_dialogue_error[(int)$_POST["dialogue_id"]] = $result;
      }
      else {
        # pass since all user dependent data is used beneath
      }
      break;


    case "decline_invitation":
      $log("decline invitation");
      $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/dialogue/decline_invitation/decline_invitation.php"))(
        $app, $_POST
      );
      if ($result instanceof RequestError) {
        $err("decline invitation error: " . $result->dev_message);
        $activate_dialogue_error[(int)$_POST["dialogue_id"]] = $result;
      }
      else {
        # pass since all user dependent data is used beneath
      }
      break;


    case "edit_profile":
      $log("edit profile");
      $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/account/edit_profile/edit_profile.php"))(
        $app, $_POST
      );
      if ($result instanceof RequestError) {
        $err("edit profile error: " . $result->dev_message);
        $edit_profile_error = $result;
      }
      else {
        # pass since all user dependent data is used beneath
      }
      break;


    case "accept_dialogue_invitation":
      $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/dialogue/accept_dialogue_invitation/accept_dialogue_invitation.php"))(
        $app, $_POST
      );
      if ($result instanceof RequestError) {
        $err("accept_dialogue_invitation error: " . $result->dev_message);
        $activate_dialogue_error[(int)$_POST["dialogue_id"]] = $result;
      }
      else {
        # pass since all user dependent data is used beneath
      }
      break;


    default:
      if (isset($_POST["action"])) {
        $warn("unknown action: " . $_POST["action"]);
      }
  }


  HtmlUtils::head();

  ?>


  <?php
  if ($app->somebody_logged_in()) {
    # main content ... -> my active dialoges
    ?>
    <nav class="w3-margin">
      <a class="button" href="/home.php">Home</a>
      <a class="button" href="/my_news.php">News</a>
      <a class="button" href="/members.php">Members</a>
      <a class="button" href="/account_settings.php">Account-Settings</a>

      <!-- TODO: logout does not work -->
      <div class="w3-right">
        <a class="delete-button" href="/index.php?tab=logout">Logout</a>
        <a class="button" style="border-color: #8bc34a; color: #8bc34a" href="/project.php"> Project-Info </a>
      </div>
    </nav>
    <?php

    match ($_GET["tab"] ?? "") {
      "settings" => (require $_SERVER["DOCUMENT_ROOT"] . "/pages/index/settings.php")(
        app: $app
      ),
      "members" => (require $_SERVER["DOCUMENT_ROOT"] . "/pages/index/members.php")(
        app: $app
      ),
      "my_news" => (require $_SERVER["DOCUMENT_ROOT"] . "/pages/index/my_news.php")(
        app: $app
      ),
      "concepts" => (require $_SERVER["DOCUMENT_ROOT"] . "/pages/index/concepts.php")(
        app: $app
      ),
      default => (require $_SERVER["DOCUMENT_ROOT"] . "/pages/index/home.php")(
        app: $app
      )
    }; # end match
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
          <?= ($login_error ?? null)?->get_error_card() ?>
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
          <?= ($register_error ?? null)?->get_error_card() ?>
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