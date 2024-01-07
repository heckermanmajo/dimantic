<?php
declare(strict_types=1);

use cls\App;
use cls\data\conversation_blue_print\ConversationBluePrint;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueRule;
use cls\data\dialoge\DialogueRuleRating;
use cls\data\space\Space;
use cls\data\space\SpaceDocument;
use cls\data\space\SpaceMembership;
use cls\Protocol;
use cls\RequestError;


if (count(debug_backtrace()) == 0) {
  include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


/**
 * @param App $app
 * @param array $post_data
 * @return array<ConversationBluePrint|SpaceDocument|SpaceMembership|Dialogue|Space>|RequestError
 */
function space_text_search(
  App   $app,
  array $post_data,
): array|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  try {

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




    return [];

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
  app: App::get(),
);