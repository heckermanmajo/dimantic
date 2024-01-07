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


function create_rule(
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

    if (!isset($post_data["dialogue_id"])) {
      return new RequestError(
        dev_message: "\$post_data[\"dialogue_id\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $dialogue_id = (int)$post_data["dialogue_id"];

    $dialogue = Dialogue::get_by_id($app->get_database(), $dialogue_id);

    // check that user is member of the dialogue
    if (!$dialogue->current_user_is_member($app)) {
      return new RequestError(
        dev_message: "You are not a member of this dialogue.",
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

    $rule_text = $my_membership->rule_draft;

    // check if emty
    if (strlen($rule_text) == 0) {
      return new RequestError(
        dev_message: "Rule text is empty.",
        code: RequestError::RULE_ERROR,
      );
    }
    // dont inster the same rule twice
    $last_rule = $dialogue->get_rules_of_dialogue($app);
    foreach ($last_rule as $rule) {
      if ($rule->rule_text == $rule_text) {
        return new RequestError(
          dev_message: "Same rule was sent before",
          code: RequestError::RULE_ERROR,
        );
      }
    }

    $rule = new DialogueRule();
    $rule->dialogue_id = $dialogue->id;
    $rule->account_id = $app->get_currently_logged_in_account()->id;
    // get latest messahe
    $last_message = $dialogue->get_last_message($app);
    $rule->post_message_id = $last_message->id;
    $rule->rule_text = $rule_text;
    $rule->save($app->get_database());

    // reset all ratings -> set them to pending

    foreach ($rule->get_current_ratings($app) as $rating) {
      // todo: create new if from active to pending
      // todo; also news if the state changes from declined to pending
      $rating->state = DialogueRuleRating::RATING_PENDING;
      $rating->save($app->get_database());
    }

    // todo: create news

    // make the draft field empty
    $my_membership->rule_draft = "";
    $my_membership->save($app->get_database());

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
  function: create_rule(...)
);