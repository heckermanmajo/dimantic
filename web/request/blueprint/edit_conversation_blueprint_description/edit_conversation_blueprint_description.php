<?php

declare(strict_types=1);

use cls\App;
use cls\data\conversation_blue_print\ConversationBluePrint;
use cls\data\space\Space;
use cls\Protocol;
use cls\RequestError;


if (count(debug_backtrace()) == 0) {
  require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


/**
 * This request edits a conversation blueprint.
 *
 * @param App $app
 * @param array $post_data
 * @return Space|RequestError
 */
function edit_conversation_blueprint_description(
  App   $app,
  array $post_data,
): ConversationBluePrint|RequestError {

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  try {

    if (!$app->somebody_logged_in()) {
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    if (!isset($post_data['blue_print_id'])) {
      return new RequestError(
        dev_message: "\$post_data['blue_print_id'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    if (!isset($post_data['description'])) {
      return new RequestError(
        dev_message: "\$post_data['description'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    # todo: check if user is allowed to create a new conversation blueprint in this space
    # todo: check if blueprint exists
    # todo: check that blueprint is not in use yet

    $blueprint = ConversationBluePrint::get_by_id($app->get_database(), (int)$post_data['blue_print_id']);

    if ($blueprint == null) {
      return new RequestError(
        dev_message: "Conversation blueprint not found.",
        code: RequestError::NOT_FOUND,
      );
    }

    $blueprint->description = $post_data['description'];

    $blueprint->save($app->get_database());

    # todo: here would be the place to match members to the blue print

    return $blueprint;

  }

  catch (Exception $e) {
    return new RequestError(
      dev_message: $e->getMessage(),
      code: RequestError::SYSTEM_ERROR,
    );
  }

}


return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: edit_conversation_blueprint_description(...),
  app: App::get(),
);