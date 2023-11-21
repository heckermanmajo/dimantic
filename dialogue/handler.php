<?php

use cls\App;
use cls\RequestError;

/**
 * @var App $app
 * @var callable $log (string,[array]):void
 * @var callable $warn (string,[array]):void
 * @var callable $err (string,[array]):void
 * @var callable $todo (string,[array]):void
 */

switch ($_POST["action"] ?? "") {

  case "register":
    #echo "write message";
    $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/account/register/register.php"))(
      $app, $_POST
    );
    if ($result instanceof RequestError) {
      $create_message_error = $result;
    }
    else {
      # pass since all user dependent data is used beneath
    }
    break;

  case "login":
    #echo "write message";
    $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/account/login/login.php"))(
      $app, $_POST
    );
    if ($result instanceof RequestError) {
      $create_message_error = $result;
    }
    else {
      # pass since all user dependent data is used beneath
    }
    break;


  case "logout":
    #echo "write message";
    $result = (require($_SERVER["DOCUMENT_ROOT"] . "/request/account/logout/logout.php"))(
      $app, $_POST
    );
    if ($result instanceof RequestError) {
      $create_message_error = $result;
    }
    else {
      # pass since all user dependent data is used beneath
      ob_get_clean();
      header("Location: /index.php");
      exit;
    }
    break;


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