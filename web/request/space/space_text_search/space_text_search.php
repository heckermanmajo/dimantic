<?php
declare(strict_types=1);

use cls\App;
use cls\data\conversation_blue_print\ConversationBluePrint;
use cls\data\space\Space;
use cls\GetDisplayCardInterface;
use cls\Protocol;
use cls\RequestError;


if (count(debug_backtrace()) == 0) {
  require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


/**
 * @param array $post_data
 * @return array<GetDisplayCardInterface>|RequestError
 */
function space_text_search(
  array $post_data,
): array|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  try {
    
    $app = App::get();

    if(!$app->somebody_logged_in()){
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    if (!isset($post_data["space_id"])) {
      return new RequestError(
        dev_message: "\$post_data[\"space_id\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    if (!isset($post_data["search_string"])) {
      return new RequestError(
        dev_message: "\$post_data[\"search_string\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $space = Space::get_by_id($app->get_database(), (int)$post_data["space_id"]);

    if ($space == null) {
      return new RequestError(
        dev_message: "Space not found.",
        code: RequestError::BAD_REQUEST,
      );
    }

    $search_string = $post_data["search_string"];

    # todo: check search string

    $results = [];

    $blueprints = ConversationBluePrint::search_by_search_text_in_space(
      $app->get_currently_logged_in_account()->id,
      $space->id,
      $search_string,
    );

    $results = array_merge($results, $blueprints);

    # todo: search in documents, memberships, dialogues, spaces


    return $results;

  }
  catch (Throwable $e) {
    return new RequestError(
      dev_message: $e->getMessage(),
      code: RequestError::SYSTEM_ERROR,
      e: $e,
    );
  }
}

return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: space_text_search(...),
);