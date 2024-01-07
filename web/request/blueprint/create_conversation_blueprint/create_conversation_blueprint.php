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
 * This request creates a new conversation blueprint.
 *
 * @param array $post_data
 * @return Space|RequestError
 */
function create_new_conversation_blueprint(
  array $post_data,
): ConversationBluePrint|RequestError {

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  $app = App::get();
  
  try {

    if (!$app->somebody_logged_in()) {
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    if (!isset($post_data['space_id'])) {
      return new RequestError(
        dev_message: "\$post_data['space_id'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    if (!isset($post_data['description'])) {
      return new RequestError(
        dev_message: "\$post_data['description'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    # todo: check if space exists
    # todo: check if user is member of space
    # todo: check if user is allowed to create a new conversation blueprint in this space

    $blueprint = ConversationBluePrint::getDefaultConfigurationDialogue();
    $blueprint->space_id = (int)$post_data['space_id'];
    $blueprint->author_id = $app->get_currently_logged_in_account()->id;
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
  function: create_new_conversation_blueprint(...),
);