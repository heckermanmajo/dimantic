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

function update_message_draft(
  array $post_data,
): \cls\data\dialoge\DialogueMembership|RequestError {

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  $log("Post_data", $post_data);

  try {
    
    $app = App::get();

    if (!$app->somebody_logged_in()) {
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    if (!isset($post_data["dialogue_id"])) {
      return new RequestError(
        dev_message: "dialogue_id is not set.",
        code: RequestError::BAD_REQUEST,
      );
    }

    if (!isset($post_data["rule_draft_content"])) {
      return new RequestError(
        dev_message: "rule_draft_content is not set.",
        code: RequestError::BAD_REQUEST,
      );
    }

    $dialogue_id = $post_data["dialogue_id"];
    $rule_draft_content = $post_data["rule_draft_content"];

    $dialogue = Dialogue::get_by_id($app->get_database(), (int)$dialogue_id);
    if (!$dialogue) {
      return new RequestError(
        dev_message: "Dialogue with id $dialogue_id does not exist.",
        code: RequestError::RULE_ERROR,
      );
    }

    $my_membership = $dialogue->get_membership_of_given_account($app->get_currently_logged_in_account()->id);
    if (!$my_membership) {
      return new RequestError(
        dev_message: "You are not a member of this dialogue.",
        code: RequestError::RULE_ERROR,
      );
    }

    if ($my_membership->state != \cls\data\dialoge\DialogueMembership::STATE_ACTIVE) {
      return new RequestError(
        dev_message: "You are not an active member of this dialogue.",
        code: RequestError::RULE_ERROR,
      );
    }

    $my_membership->rule_draft = $rule_draft_content;

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
  function: update_message_draft(...),
);
