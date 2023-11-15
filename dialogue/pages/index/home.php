<?php

use cls\App;
use cls\data\dialoge\Dialogue;

return function(App $app){
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
};