<?php
declare(strict_types=1);

use cls\App;
use cls\data\dialoge\Dialogue;
use cls\Protocol;
use cls\RequestError;


if (count(debug_backtrace()) == 0) {
  require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


function update_private_rules(
  array $post_data,
): \cls\data\dialoge\DialogueMembership|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  try {
    
    $app = App::get();

    $log("Post_data", $post_data);
    if($post_data == []){
      $err("Post data is completely empty.");
    }

    if(!$app->somebody_logged_in()){
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    if (!isset($post_data["dialogue_id"])) {
      return new RequestError(
        dev_message: "\$post_data[\"dialogue_id\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $dialogue_id = (int)$post_data["dialogue_id"];

    if (!isset($post_data["notes_field"])) {
      return new RequestError(
        dev_message: "\$post_data[\"notes_field\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $dialogue = Dialogue::get_by_id($app->get_database(), $dialogue_id);
    if(!$dialogue->current_user_is_member($app)){
      return new RequestError(
        dev_message: "You are not a member of this dialogue.",
        code: RequestError::RULE_ERROR,
      );
    }

    $my_membership = $dialogue->get_membership_of_given_account( $app->get_currently_logged_in_account()->id);
    if ($my_membership == null) {
      # todo_: extra-bad error  -> why is this happening?
      return new RequestError(
        dev_message: "You are not a member of this dialogue.",
        code: RequestError::RULE_ERROR,
      );
    }

    $my_membership->notes_field = $post_data["notes_field"];
    $my_membership->save($app->get_database());

    return $my_membership;
  }
  catch (Throwable $e) {
    return new RequestError(
      dev_message: $e->getMessage(),
      code: RequestError::SYSTEM_ERROR,
    );
  }
}

return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: update_private_rules(...)
);
