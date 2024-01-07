<?php
declare(strict_types=1);

use cls\App;

use cls\data\dialoge\DialogueMessageSelectionLike;
use cls\Protocol;
use cls\RequestError;

if (count(debug_backtrace()) == 0) {
  require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


# todo: problem: what if the same word/phrase occurs multiple times in the same message?
#       this makes it necessary to store the selection number in the database as well
function create_selection_like(
  App   $app,
  array $post_data,
): DialogueMessageSelectionLike|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  #if (!isset($post_data["username_or_email"])) {
  #  return new RequestError(
  #    dev_message: "\$post_data[\"username_or_email\"] not set",
  #    code: RequestError::BAD_REQUEST,
  #  );
  #}

  try {

    if (!$app->somebody_logged_in()) {
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    # dialogue_message_id
    if (!isset($post_data["dialogue_message_id"])) {
      return new RequestError(
        dev_message: "\$post_data[\"dialogue_message_id\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }
    # liked_selection
    if (!isset($post_data["liked_selection"])) {
      return new RequestError(
        dev_message: "\$post_data[\"liked_selection\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $dialogue_message_id = (int)$post_data["dialogue_message_id"];
    $liked_selection = $post_data["liked_selection"];

    $dialoge_message = \cls\data\dialoge\DialogueMessage::get_by_id(
      $app->get_database(),
      id: $dialogue_message_id,
    );

    if ($dialoge_message === null) {
      return new RequestError(
        dev_message: "No dialogue message found with id: $dialogue_message_id",
        code: RequestError::NOT_FOUND,
      );
    }

    $dialogue = $dialoge_message->get_dialogue($app);
    $my_potential_membership = $dialogue->get_membership_of_given_account(
      $app,
      $app->get_currently_logged_in_account()->id,
    );
    if ($my_potential_membership === null) {
      return new RequestError(
        dev_message: "You are not a member of this dialogue.",
        code: RequestError::RULE_ERROR,
      );
    }

    # can we like the selection, or is it already liked?
    # and have we enough like credits?

    $free_like_credits = $my_potential_membership->get_absolute_amount_of_FREE_like_credits($app);

    $enough_like_credits = $free_like_credits >= strlen($liked_selection);

    if (!$enough_like_credits) {
      return new RequestError(
        dev_message: "You don't have enough like credits.",
        code: RequestError::RULE_ERROR,
      );
    }

    # todo: prevent inserting the same selection twice

    $selection = new DialogueMessageSelectionLike();
    $selection->dialogue_message_id = $dialogue_message_id;
    $selection->dialogue_id = $dialogue->id;
    $selection->account_id = $app->get_currently_logged_in_account()->id;
    $selection->selection = $liked_selection;
    $selection->created_at = time();
    $selection->save($app->get_database());

    return $selection;


  } catch (Throwable $e) {
    return new RequestError(
      dev_message: $e->getMessage(),
      code: RequestError::SYSTEM_ERROR
    );
  }
}


return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: create_selection_like(...),
  app: App::get(),
);