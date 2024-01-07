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
 * This requests publishes a conversation blueprint.
 *
 * @param App $app
 * @param array $post_data
 * @return Space|RequestError
 */
function unpublish_blueprint(
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

    $blueprint = ConversationBluePrint::get_by_id($app->get_database(), (int)$post_data['blue_print_id']);

    if ($blueprint == null) {
      return new RequestError(
        dev_message: "Conversation blueprint not found.",
        code: RequestError::NOT_FOUND,
      );
    }

    if (
      $blueprint->user_is_allowed_to_unpublish_blueprint(
        $app,
        $app->get_currently_logged_in_account()->id
      ) === false
    ) {
      return new RequestError(
        dev_message: "You are not allowed to unpublish this blueprint.",
        code: RequestError::RULE_ERROR,
      );
    }

    if ($blueprint->published === 0) {
      return new RequestError(
        dev_message: "This blueprint is already unpublished.",
        code: RequestError::RULE_ERROR,
      );
    }

    $blueprint->published = 0;

    $blueprint->save($app->get_database());

    # todo: send notifications/invitations, etc.

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
  function: unpublish_blueprint(...),
  app: App::get(),
);