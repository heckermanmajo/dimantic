<?php

use cls\controller\request\attention_profile\CreateAttentionProfile;
use cls\controller\request\attention_profile\SelectOtherAttentionProfile;

include $_SERVER['DOCUMENT_ROOT'] . '/cls/App.php';


App::head_html();
if (!App::somebody_is_logged_in()) {
  header('Location: /index.php');
  exit();
}

if (isset($_POST["action"])) {

  if ($_POST["action"] == "create_attention_profile") {
    $result = CreateAttentionProfile::execute();
    if ($result instanceof \cls\RequestError) {
      echo $result->dev_message;
      # todo: handle better ....
      exit();
    }
    else {
      // pass ...
    }
  }


  if ($_POST["action"] == "select_other_attention_profile") {
    $result = SelectOtherAttentionProfile::execute();
    if ($result instanceof \cls\RequestError) {
      echo $result->dev_message;
      # todo: handle better ....
      exit();
    }
    else {
      // pass ...
    }
  }

}

?>

  <header class="w3-row w3-margin">
    <div class="w3-half">
      <a class="w3-button" href="/account.php"> My Attention Profiles </a>
      <a class="w3-button" href="/account.php?tab=settings"> Settings </a>
      <a class="w3-button" href="/account.php?tab=geistmark"> Geistmark </a>
    </div>
    <div class="w3-half" style="text-align: right">
      <?php App::echo_nav_bar(); ?>
      <form method="post" style="display: inline-block" action="/index.php">
        <input type="hidden" name="action" value="logout">
        <button class="delete-button"> Logout</button>
      </form>
    </div>
  </header>

<?php
switch ($_GET["tab"] ?? ""):
  #################################################################################
  case "settings":
    ?>
    <div class="w3-row">
      <div class="w3-half">
        <h3>Settings list </h3>
      </div>
      <div class="w3-half">
        <h3> details of settings </h3>
      </div>
    </div>
    <?php break; ?>
  <?php
  #################################################################################
  case "geistmark":
    ?>
    <div class="w3-row">
      <div class="w3-half">
        <h3>Geistmark Transactions & current amount </h3>
      </div>
      <div class="w3-half">
        <h3> details of settings </h3>
      </div>
    </div>
    <?php break; ?>
  <?php
  #################################################################################
  default:
    ?>
    <div class="w3-row">
      <div class="w3-half">
        <form method="post" class="w3-margin">
          <input type="hidden" name="action" value="create_attention_profile">
          <input class="w3-margin" type="text" name="title" placeholder="Title"><br>
          <textarea class="w3-margin" name="description" placeholder="Title"></textarea><br>
          <button class="button">Create new Attention Profile</button>
        </form>
        <?php
        $attention_profiles = \cls\data\attention_profile\AttentionProfile::get_array(
          App::get_connection(),
          "SELECT * FROM `AttentionProfile` WHERE owner_member_id = ?;",
          [App::get_current_account()->id]
        );
        foreach ($attention_profiles as $profile) {
          echo $profile->get_manage_card();
        }
        ?>
      </div>
      <div class="w3-half">
      </div>
    </div>
  <?php

endswitch;
echo "<pre>";
echo json_encode(App::$attention_profile, JSON_PRETTY_PRINT);
echo "</pre>";


?>
<br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br>

