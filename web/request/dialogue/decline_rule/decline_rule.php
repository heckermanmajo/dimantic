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


function decline_rule(
  App   $app,
  array $post_data,
): DialogueRuleRating|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  try {

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

    $rule_id = (int)$post_data["rule_id"];

    if (!isset($post_data["rule_id"])) {
      return new RequestError(
        dev_message: "\$post_data[\"rule_id\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }


    if (!isset($post_data["reason_text"])) {
      return new RequestError(
        dev_message: "\$post_data[\"reason_text\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $reason_text = (string)$post_data["reason_text"];

    $dialogue_id = (int)$post_data["dialogue_id"];

    $dialogue = Dialogue::get_by_id($app->get_database(), $dialogue_id);

    $membership = $dialogue->get_membership_of_given_account(
      $app,
      $app->get_currently_logged_in_account()->id,
    );

    if ($membership == null) {
      return new RequestError(
        dev_message: "You are not a member of this dialogue.",
        code: RequestError::RULE_ERROR,
      );
    }

    $rule = DialogueRule::get_by_id($app->get_database(), $rule_id);

    if ($rule == null) {
      return new RequestError(
        dev_message: "Rule with id $rule_id not found.",
        code: RequestError::BAD_REQUEST,
      );
    }

    // is there a rating of me for this rule already?
    $possible_rating = DialogueRuleRating::get_one(
      $app->get_database(),
      "SELECT * FROM DialogueRuleRating WHERE dialogue_rule_id = :rule_id AND `account` = :account_id",
      ["rule_id" => $rule->id, "account_id" => $app->get_currently_logged_in_account()->id],
    );

    if ($possible_rating != null) {
      # in case already accepted -> error, cannot decline anymore
      if($possible_rating->rating === DialogueRuleRating::RATING_ACCEPT){
        return new RequestError(
          dev_message: "You already accepted this rule.",
          code: RequestError::RULE_ERROR,
        );
      }
      # in case of declined
      if($possible_rating->rating === DialogueRuleRating::RATING_REJECT){
        $possible_rating->reason_text = $reason_text;
        $possible_rating->save($app->get_database());
        return $possible_rating;
      }

      # in case of pending -> set to declined
      $possible_rating->rating = DialogueRuleRating::RATING_REJECT;
      $possible_rating->reason_text = $reason_text;
      $possible_rating->save($app->get_database());
      return $possible_rating;
    }

    $rating = new DialogueRuleRating();
    $rating->dialogue_rule_id = $rule->id;
    $rating->account = $app->get_currently_logged_in_account()->id;
    $rating->rating = DialogueRuleRating::RATING_REJECT;
    $rating->reason_text = $reason_text;

    $rating->save($app->get_database());

    // todo: create news entry for all members of the dialogue
    #  todo: special news for the author of the rule

    return $rating;

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
  function: decline_rule(...),
  app: App::get(),
);