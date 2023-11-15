<?php

use cls\App;
use cls\data\dialoge\Dialogue;

return function(App $app) {
  ?>
  <div class="w3-margin">
    <a class="button" href="/index.php?tab=explore&filter=closed">Finished</a>
    <a class="button" href="/index.php?tab=explore&filter=active">Ongoing</a>
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
        <h3>Ongoing Dialoges</h3>
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
};