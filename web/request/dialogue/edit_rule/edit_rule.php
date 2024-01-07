<?php
declare(strict_types=1);

use cls\App;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueRule;
use cls\data\dialoge\DialogueRuleRating;
use cls\Protocol;
use cls\RequestError;


if (count(debug_backtrace()) == 0) {
  require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


function edit_rule(
  array $post_data,
): DialogueRule|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  try {

    $app = App::get();
    
    if(!$app->somebody_logged_in()){
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    if (!isset($post_data["dialogue_rule_id"])) {
      return new RequestError(
        dev_message: "\$post_data[\"dialogue_rule_id\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $dialogue_rule_id = (int)$post_data["dialogue_rule_id"];

    if (!isset($post_data["dialogue_id"])) {
      return new RequestError(
        dev_message: "\$post_data[\"dialogue_id\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $dialogue_id = (int)$post_data["dialogue_id"];

    $dialogue = Dialogue::get_by_id($app->get_database(), $dialogue_id);

    if (!isset($post_data["rule_text"])) {
      return new RequestError(
        dev_message: "\$post_data[\"rule_text\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $rule_text = (string)$post_data["rule_text"];

    $rule = DialogueRule::get_by_id($app->get_database(), $dialogue_rule_id);

    if ($rule == null) {
      return new RequestError(
        dev_message: "Rule with id $dialogue_rule_id not found.",
        code: RequestError::BAD_REQUEST,
      );
    }

    if($rule->account_id != $app->get_currently_logged_in_account()->id) {
      return new RequestError(
        dev_message: "You are not the author of this rule.",
        code: RequestError::RULE_ERROR,
      );
    }

    $rule->rule_text = $rule_text;

    $rule->save($app->get_database());

    // reset all ratings -> set them to pending

    /**
     * @var DialogueRuleRating $rating
     */
    foreach ($rule->get_current_ratings($app) as $rating) {
      // todo: create new if from active to pending
      // todo; also news if the state changes from declined to pending
      $rating->rating = DialogueRuleRating::RATING_PENDING;
      $rating->save($app->get_database());
    }

    // todo: create news

    return $rule;
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
  function: edit_rule(...)
);