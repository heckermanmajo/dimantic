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




  HtmlUtils::footer($app);
}
catch (Throwable $e) {
  App::dump_logs(t: $e);
}