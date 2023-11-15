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
    = App::get_logging_functions(__CLASS__, __FUNCTION__);


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
    HtmlUtils::main_header();
    # main content ... -> my active dialoges
    ?>
    <nav class="w3-card-4 w3-margin w3-padding">
      <a class="button" href="/">Home</a>
      <a class="button" href="/index.php?tab=my_news">News</a>
      <a class="button" href="/index.php?tab=explore">Explore</a>
      <a class="button" href="/index.php?tab=members">Members</a>
      <a class="button" href="/index.php?tab=settings">Account-Settings</a>

      <!-- TODO: logout does not work -->
      <a class="button" href="/index.php?tab=logout">Logout</a>
      <div class="w3-right">
        <a class="button" style="border-color: #8bc34a; color: #8bc34a" href="/project.php"> Project-Info </a>
      </div>
    </nav>
    <?php

    switch ($_GET["tab"] ?? "") {


      ####################################################################
      #
      #
      #
      #
      #
      ####################################################################
      case "explore":
        ?>
        <div class="w3-margin">
          <a class="button" href="/index.php?tab=explore&filter=closed">Finished</a>
          <a class="button" href="/index.php?tab=explore&filter=active">Open</a>
          <a class="button" href="/index.php?tab=explore&filter=new">New</a>

          <?php

          switch ($_GET["filter"] ?? "closed"):

            case "closed":
              ?>
              <h3>Finished Dialoges</h3>
              <?php
              $dialoges = Dialogue::get_dialogues_by_state(
                offset: 0,
                limit: 50,
                state: Dialogue::STATE_CLOSED,
                app: $app
              );
              foreach ($dialoges as $dialoge) {
                echo $dialoge->get_overview_card($app);
              }
              ?>
              <?php
              break;


            case ("active"):


              ?>
              <h3>Open Dialoges</h3>
              <?php
              $dialoges = Dialogue::get_dialogues_by_state(
                offset: 0,
                limit: 50,
                state: Dialogue::STATE_OPEN,
                app: $app
              );
              foreach ($dialoges as $dialoge) {
                echo $dialoge->get_overview_card($app);
              }
              ?>
              <?php
              break;


            case("new"):

              ?>
              <h3>New Dialoges</h3>
              <?php
              $dialoges = Dialogue::get_dialogues_by_state(
                offset: 0,
                limit: 50,
                state: Dialogue::STATE_NOT_YET_STARTED,
                app: $app
              );
              foreach ($dialoges as $dialoge) {
                echo $dialoge->get_overview_card($app);
              }
              ?>
              <?php
              break;

          endswitch;
          ?>
        </div>
        <?php
        break;


      case "settings":
        ?>
        <div class="info-card">

          <p> <img src="/res/info.png" width="60"> Your Profile </p>
          <p>Write some text about you:</p>
          <ul>
            <li>What topics dou you want to talk about?</li>
            <li>What is your intellectual mission?</li>
            <li>What is your expertise?</li>
          </ul>
          <p> For your <b>Profile-Image</b> use Gravatar: <a style="color: dodgerblue" href="https://de.gravatar.com/">https://de.gravatar.com/</a> </p>
          <p> There you can provide a profile image and associate it with your email. </p>
          <p> Wordpress and GitHub use it too. </p>
        </div>

        <form method="post" class="w3-card w3-margin w3-padding">
          <input type="hidden" name="action" value="edit_profile">
          <label>
            <span style="margin-bottom: 10px;display: inline-block">
              <i>Profile Description (can be read by everybody) - don't forget to click save: </i></span>
            <br>
          <textarea rows="10" cols="100"
                    name="content"><?= $app->get_currently_logged_in_account()->content ?></textarea>
          </label>
          <br>
          <button class="button" type="submit"> Save ✅ </button>
        </form>
        <?php

        break;

      case "members":
        $all_members = Account::get_all_accounts(0, 50, $app);
        foreach ($all_members as $member) {
          echo $member->get_display_card($app);
        }

        break;

      ####################################################################
      #
      #
      #
      #
      #
      ####################################################################
      case "my_news":
        $my_news = NewsEntry::get_my_news($app);
        ?>
        <div class="w3-margin">
          <?php
          foreach ($my_news as $news_entry) {
            echo $news_entry->get_news_card($app);
          }
          ?>
        </div>
        <?php
        break;

      ####################################################################
      #
      #
      #
      #
      #
      ####################################################################
      default:
        # open/closed
        ?>
        <div>
          <a class="button w3-margin" href="/dialogue.php"> Create Dialoge </a>
          <hr>
          <a class="button w3-margin w3-padding" href="/index.php?mode=my_ongoing_dialogues">
            My currently ongoing dialogues
          </a>
          <a class="button w3-margin w3-padding" href="/index.php?mode=my_invitations">
            Dialogues I am invited to
          </a>
          <a class="button w3-margin w3-padding" href="/index.php?mode=accepted_but_not_started">
            Dialogues ready to start
          </a>
          <?php
          if (isset($_GET["mode"]) && $_GET["mode"] == "my_invitations") {
            $dialoges = Dialogue::get_dialogues_i_am_invited_to(0, 50, $app);
          }
          elseif (isset($_GET["mode"]) && $_GET["mode"] == "my_ongoing_dialogues") {
            $dialoges = Dialogue::get_my_ongoing_dialogues(0, 50, $app);
          }
          elseif (isset($_GET["mode"]) && $_GET["mode"] == "accepted_but_not_started") {
            $dialoges = Dialogue::my_dialogues_ready_to_start(0, 50, $app);
          }
          else {
            $dialoges = Dialogue::get_my_dialoges(0, 50, $app);
          }
          foreach ($dialoges as $dialoge) {
            echo $dialoge->get_overview_card(
              $app,
              activate_error: $activate_dialogue_error[$dialoge->id] ?? null
            );
          }
          ?>

        </div>

        <?php
        break;
    }
  }
  else {
    ?>
    <div class="w3-row">
      <div class="w3-col m9 s9 l9">
        <div class="w3-card-4 w3-margin w3-padding">
          <h3>Dialoge</h3>

          <?=$_SERVER["DOCUMENT_ROOT"]?>
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