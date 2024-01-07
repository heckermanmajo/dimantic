<?php

declare(strict_types=1);

use cls\App;
use cls\data\conversation_blue_print\ProtoRule;
use cls\Protocol;
use cls\RequestError;


if (count(debug_backtrace()) == 0) {
  require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


/**
 * This request creates a new proto-rule for a conversation blueprint.
 *
 * @param array $post_data
 * @return ProtoRule|RequestError
 */
function get_proto_rule_card(
  array $post_data,
): string|RequestError {

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  try {
    
    $app = App::get();

    if (!$app->somebody_logged_in()) {
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    if (!isset($post_data['proto_rule_id'])) {
      return new RequestError(
        dev_message: "\$post_data['proto_rule_id'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    # todo: check that blueprint exists and is not in use and user is allowed to edit it

    $proto_rule = ProtoRule::get_by_id(
      $app->get_database(),
      (int)$post_data['proto_rule_id']
    );

    if ($proto_rule === null) {
      return new RequestError(
        dev_message: "Proto rule not found",
        code: RequestError::NOT_FOUND,
      );
    }

    return $proto_rule->get_card();

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
  function: get_proto_rule_card(...)
);