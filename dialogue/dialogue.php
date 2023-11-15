<?php
declare(strict_types=1);

use cls\App;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMessage;
use cls\RequestError;

include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";

try {
  App::init_context(basename(__FILE__));
  $app = App::get();
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__);

  if (!$app->somebody_logged_in()) {
    header("Location: /index.php");
    exit;
  }

  if (!isset($_GET["id"])) {

    # if we directly start a dialogue with a partner (on the profile page)
    # we also get the partner_id in the url
    if (isset($_GET["partner_id"])) {
      $post_data = $_POST;
      $post_data["partner_id"] = $_GET["partner_id"];
    }

    $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/dialogue/create_dialogue/create_dialogue.php"))(
      $app, $post_data ?? $_POST
    );
    if ($result instanceof RequestError) {
      #$create_message_error = $result;
      $result->get_error_card();
      exit();
    }
    else {
      header("Location: /dialogue.php?id=" . $result->id);
      exit();
    }


  }
  $invite_error = null;
  switch ($_POST["action"] ?? "") {

    case "write_message":
      #echo "write message";
      $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/dialogue/write_message/write_message.php"))(
        $app, $_POST
      );
      if ($result instanceof RequestError) {
        $create_message_error = $result;
      }
      else {
        # pass since all user dependent data is used beneath
      }
      break;
    case "invite_account_into_dialogue":
      #echo "write message";
      $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/dialogue/invite_into_dialogue/invite_into_dialogue.php"))(
        $app, $_POST
      );
      if ($result instanceof RequestError) {
        $invite_error = $result;
      }
      else {
        # pass since all user dependent data is used beneath
      }
      break;
    case "edit_dialogue":
      #echo "write message";
      $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/dialogue/edit_dialogue/edit_dialogue.php"))(
        $app, $_POST
      );
      if ($result instanceof RequestError) {
        $edit_dialogue_error = $result;
      }
      else {
        # pass since all user dependent data is used beneath
      }
      break;
    case "activate_dialogue":
      $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/dialogue/start_dialogue/start_dialogue.php"))(
        $app, $_POST
      );
      if ($result instanceof RequestError) {
        $activate_dialogue_error[(int)$_POST["dialogue_id"]] = $result;
      }
      else {
        # pass since all user dependent data is used beneath
      }
      break;
    case "decline_invitation":
      $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/dialogue/decline_invitation/decline_invitation.php"))(
        $app, $_POST
      );
      if ($result instanceof RequestError) {
        $activate_dialogue_error[(int)$_POST["dialogue_id"]] = $result;
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
      break;
  }

  $dialogue = Dialogue::get_by_id($app->get_database(), (int)$_GET["id"]);

  \cls\HtmlUtils::head();

  ?>
  <br>
  <a class="button w3-margin w3-padding" href="/index.php"> ZURÜCK </a>

  <?php
  if ($dialogue->author_id == $app->get_currently_logged_in_account()->id && $dialogue->state == Dialogue::STATE_NOT_YET_STARTED) {
    ?>
    <a class="button w3-margin w3-padding" href="/dialogue.php?id=<?= $dialogue->id ?>&tab=edit_dialoge"> DIALOG
      BEARBEITEN </a>
    <?php
  }
  ?>
  <?php

  switch ($_GET["tab"] ?? "") {


    case "invite_member":

      if (isset($invite_error) && $invite_error instanceof RequestError) {
        echo $invite_error->get_error_card();
      }
      $all_accounts = \cls\data\account\Account::get_all_accounts(0, 50, $app);
      $all_memberships = $dialogue->get_memberships($app);
      echo "<pre>";
      var_dump($all_memberships);
      echo "</pre>";
      if ($dialogue->get_number_of_memberships($app) >= 2) {
        ?>
        <div style="color: orangered">dialogue is full</div>
        <?php
        foreach ($all_memberships as $membership) {
          ?>
          <?= $membership->get_info_bar($app) ?>
          <?= $membership->get_associated_account($app->get_database())->get_display_card($app) ?>
          <?php
        }
      }
      else {
        foreach ($all_accounts as $account) {

          if ($account->id == $app->get_currently_logged_in_account()->id) {
            continue;
          }
          ?>
          <?= $account->get_display_card($app) ?>
          <form method="post">
            <input type="hidden" name="action" value="invite_account_into_dialogue">
            <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
            <input type="hidden" name="account_id" value="<?= $account->id ?>">
            <button class="button">INVITE TO DIALOGUE: <?= $account->name ?></button>
          </form>
          <?php
        }
      }
      break;


    case "edit_dialoge":
      ?>
      <form method="post" class="w3-card w3-margin w3-padding">
        <input type="hidden" name="action" value="edit_dialogue">
        <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
        <!--<label>
        Max number of days to reply.
        <input type="number" name="number_of_days_to_reply" value="<?= $dialogue->number_of_days_to_reply ?>">
      </label>
      <label>
        Message cooldown in hours.
        <input type="number" name="message_cooldown_in_hours" value="<?= $dialogue->message_cooldown_in_hours ?>">
      </label>-->
        <label>
          <textarea name="content"><?= $dialogue->content ?></textarea>
        </label>
        <button class="button">Edit Dialogue</button>
      </form>

      <?php
      break;


    default:
      if ($dialogue->state == Dialogue::STATE_NOT_YET_STARTED) {
        echo $dialogue->get_overview_card($app);
        ?>
        <pre><?= json_encode($dialogue, JSON_PRETTY_PRINT) ?></pre>
        <pre><?= json_encode($dialogue->get_memberships($app), JSON_PRETTY_PRINT) ?></pre>
        <a class="button" href="/dialogue.php?id=<?= $dialogue->id ?>&tab=invite_member">invite member</a>
        <?php
      }
      elseif ($dialogue->state == Dialogue::STATE_OPEN) {
        echo $dialogue->get_overview_card($app);
        $messages = DialogueMessage::get_all_messages_of_dialogue($app, $dialogue->id);
        ?>
        <div>
          <?php if ($dialogue->next_turn_is_my_turn($app)): ?>
            <?= ($create_message_error ?? null)?->get_error_card() ?>
            <p>It is your turn to write a message.</p>
            <form method="post" class="w3-margin w3-padding w3-card-4">
              <input type="hidden" name="action" value="write_message">
              <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
              <label>
                <textarea name="content" rows="6" cols="50"></textarea>
              </label>
              <button class="button">SEND</button>
            </form>
          <?php else: ?>
            <div class="w3-card w3-margin w3-padding">
              <p>It is not your turn to write a message.</p>
              <p>You will get a news entry when your partner has answered.</p>
            </div>
          <?php endif; ?>
          <!-- <h2>WRITE MESSAGES!!!</h2>-->
          <?php
          foreach ($messages as $message) {
            echo $message->get_view_card($app);
          }
          ?>

        </div>
        <?php
      }
      break;
  }

  \cls\HtmlUtils::footer($app);
}
catch (\Throwable $e) {
  App::dump_logs(t: $e);
}