<?php
declare(strict_types=1);

use cls\App;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueRule;
use cls\Protocol;
use cls\RequestError;


if (count(debug_backtrace()) == 0) {
  include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


function delete_rule(
  App   $app,
  array $post_data,
): null|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  try {
    /**
     * <input type="hidden" name="action" value="delete_rule">
     * <input type="hidden" name="dialogue_rule_id" value="<?= $this->id ?>">
     * <input type="hidden" name="dialogue_id" value="<?= $dialogue->id ?>">
     */
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

    // check that user is author of rule
    if ($dialogue->author_id != $app->get_currently_logged_in_account()->id) {
      return new RequestError(
        dev_message: "You are not the author of this rule.",
        code: RequestError::RULE_ERROR,
      );
    }

    $rule = DialogueRule::get_by_id($app->get_database(), $dialogue_rule_id);

    if ($rule == null) {
      return new RequestError(
        dev_message: "Rule with id $dialogue_rule_id not found.",
        code: RequestError::BAD_REQUEST,
      );
    }

    $rule->delete($app->get_database());

    return null;
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
  function: delete_rule(...),
  app: App::get(),
);