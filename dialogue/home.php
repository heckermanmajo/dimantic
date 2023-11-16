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

  App::init_context(basename_file: basename(path:__FILE__));
  $app = App::get();
  $app->init_database();
  [$log, $warn, $err, $todo]
    = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  if (!$app->somebody_logged_in()) {
    # header("Location: /index.php");
  }

  switch ($_POST["action"] ?? "") {

    #case "accept_dialogue_invitation":
    #$result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/dialogue/accept_dialogue_invitation/accept_dialogue_invitation.php"))(
    #  $app, $_POST
    #);
    #if ($result instanceof RequestError) {
    #  $err("accept_dialogue_invitation error: " . $result->dev_message);
    #  $activate_dialogue_error[(int)$_POST["dialogue_id"]] = $result;
    #}
    #else {
    #  # pass since all user dependent data is used beneath
    #}
    #break;

    default:
      if (isset($_POST["action"])) {
        $warn("unknown action: " . $_POST["action"]);
      }
  }

  HtmlUtils::head();

  ?>
  <div>
    <!--<a class="button w3-margin" href="/dialogue.php"> Create Dialoge </a>
    <hr>-->
    <div class="w3-margin">
      <button
        class="button"
        onclick="FN_TOGGLE('home_info')"
      >
        <img src="/res/info.png" width="30">
      </button>
      <a
        style="font-size: 80%"
        class="button"
        href="/index.php?mode=my_ongoing_dialogues">
        My currently ongoing dialogues
      </a>
      <a
        class="button"
        href="/index.php?mode=my_invitations"
        style="font-size: 80%"
      >
        Dialogues I am invited to
      </a>
      <a
        class="button"
        href="/index.php?mode=accepted_but_not_started"
        style="font-size: 80%"
      >
        Dialogues ready to start
      </a>
    </div>
    <div onclick="FN_TOGGLE('home_info')" class="info-card" id="home_info" style="display:none">
      <p>Home info .... </p>
    </div>
    <?php
    if (isset($_GET["mode"]) && $_GET["mode"] == "my_invitations") {
      $dialoges = Dialogue::get_dialogues_i_am_invited_to(0, 50, $app);
    }
    elseif (isset($_GET["mode"]) && $_GET["mode"] == "accepted_but_not_started") {
      $dialoges = Dialogue::my_dialogues_ready_to_start(0, 50, $app);
    }
    else { #if (isset($_GET["mode"]) && $_GET["mode"] == "my_ongoing_dialogues") {
      $dialoges = Dialogue::get_my_ongoing_dialogues(0, 50, $app);
    }
    #else {
    #  $dialoges = Dialogue::get_my_dialoges(0, 50, $app);
    #}
    foreach ($dialoges as $dialoge) {
      echo $dialoge->get_overview_card(
        $app,
        activate_error: $activate_dialogue_error[$dialoge->id] ?? null
      );
    }
    ?>

  </div>

  <?php


  HtmlUtils::footer($app);
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}