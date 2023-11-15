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
      <a class="button" href="/">Home</a>
      <a class="button" href="/index.php?tab=my_news">News</a>
      <a class="button" href="/index.php?tab=explore">Explore</a>
      <a class="button" href="/index.php?tab=members">Members</a>
      <a class="button" href="/index.php?tab=interest">Interests</a>
      <a class="button" href="/index.php?tab=vaults">Vaults</a>
      <a class="button" href="/index.php?tab=concepts">Concepts</a>
      <a class="button" href="/index.php?tab=settings">Account-Settings</a>

      <!-- TODO: logout does not work -->
      <div class="w3-right">
        <a class="delete-button" href="/index.php?tab=logout">Logout</a>
        <a class="button" style="border-color: #8bc34a; color: #8bc34a" href="/project.php"> Project-Info </a>
      </div>
    </nav>
    <?php

    match ($_GET["tab"] ?? "") {
      "explore" => (require $_SERVER["DOCUMENT_ROOT"] . "/pages/index/explore.php")(
        app: $app
      ),
      "settings" => (require $_SERVER["DOCUMENT_ROOT"] . "/pages/index/settings.php")(
        app: $app
      ),
      "members" => (require $_SERVER["DOCUMENT_ROOT"] . "/pages/index/members.php")(
        app: $app
      ),
      "interest" => (require $_SERVER["DOCUMENT_ROOT"] . "/pages/index/interest.php")(
        app: $app
      ),
      "my_news" => (require $_SERVER["DOCUMENT_ROOT"] . "/pages/index/my_news.php")(
        app: $app
      ),
      "vaults" => (require $_SERVER["DOCUMENT_ROOT"] . "/pages/index/vaults.php")(
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
    (require $_SERVER["DOCUMENT_ROOT"] . "/pages/index/login_reg_page.php")(
      login_error: $login_error ?? null,
      register_error: $register_error ?? null,
      app: $app
    );
  }

  HtmlUtils::footer($app);
}
catch (\Throwable $e) {
  App::dump_logs(t: $e);
}