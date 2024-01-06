<?php
declare(strict_types=1);

use cls\App;
use cls\data\account\Account;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMembership;
use cls\data\conversation_blue_print\ConversationBluePrint;
use cls\Protocol;
use cls\RequestError;

if (count(debug_backtrace()) == 0) {
  include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}

/**
 * This request creates a dialogue by a blueprint.
 *
 * @param App $app
 * @param array $post_data
 * @return Dialogue|RequestError
 */
function create_dialogue_by_blueprint(
  App   $app,
  array $post_data,
): Dialogue|RequestError {
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

    $blue_print_id = (int)$post_data['blue_print_id'];

    if (!isset($post_data['account_ids'])) {
      return new RequestError(
        dev_message: "\$post_data['account_ids'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $account_ids = $post_data['account_ids'];
    $account_ids = array_map(callback: intval(...), array: $account_ids);

    if (!isset($post_data['possible_moderator_id'])) { # 0 -> null or int
      return new RequestError(
        dev_message: "\$post_data['possible_moderator_id'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }
    $possible_moderator_id = $post_data['possible_moderator_id'];
    if ($possible_moderator_id === 0) {
      $possible_moderator_id = null;
    }


    if (!isset($post_data['create_news_entries'])) {  # 1 or 0
      return new RequestError(
        dev_message: "\$post_data['create_news_entries'] not set",
        code: RequestError::BAD_REQUEST,
      );
    }

    $create_news_entries = $post_data['create_news_entries'];
    if ($create_news_entries === 0) {
      $create_news_entries = false;
    }
    else {
      $create_news_entries = true;
    }


    $accounts = [];
    foreach ($account_ids as $account_id) {
      $account = Account::get_by_id(
        pdo: $app->get_database(),
        id: $account_id
      );
      if ($account == null) {
        return new RequestError("Account with id $account_id does not exist.", RequestError::NOT_FOUND);
      }
      $accounts[] = $account;
    }

    $moderator_account = null;
    if ($possible_moderator_id !== null) {

      $moderator_account = Account::get_by_id(
        pdo: $app->get_database(),
        id: $possible_moderator_id
      );

      if ($moderator_account == null) {
        return new RequestError("Account with id $possible_moderator_id does not exist.", RequestError::NOT_FOUND);
      }

    }

    $blue_print = ConversationBluePrint::get_by_id(
      pdo: $app->get_database(),
      id: $blue_print_id
    );

    if ($blue_print == null) {
      return new RequestError("DialogueBluePrint with id $blue_print_id does not exist.", RequestError::NOT_FOUND);
    }

    $dialogue = new Dialogue();
    $dialogue->author_id = $blue_print->author;
    $dialogue->blue_print_id = $blue_print->id;
    $dialogue->created_at = time();
    $dialogue->state = Dialogue::STATE_OPEN;

    $dialogue->save($app->get_database());

    foreach ($accounts as $account) {
      $membership = new DialogueMembership();
      $membership->account_id = $account->id;
      $membership->dialogue_id = $dialogue->id;
      $membership->state = DialogueMembership::STATE_ACTIVE;

      $membership->save($app->get_database());
    }

    # Create the moderator membership
    if (isset($moderator_account) && $moderator_account != null) {
      $membership = new DialogueMembership();
      $membership->account_id = $moderator_account->id;
      $membership->dialogue_id = $dialogue->id;
      $membership->state = DialogueMembership::STATE_MODERATOR;

      $membership->save($app->get_database());
    }

    if ($create_news_entries) {
      foreach ($accounts as $account) {
        #$news_entry = new NewsEntry();
        #$news_entry->account_id = $account->id;
        #$news_entry->type = NewsEntry::TYPE_DIALOGUE_HAS_STARTED;
        #$news_entry->dialogue_id = $dialogue->id;
        #$news_entry->save($app->get_database());
      }

      if (isset($moderator_account) && $moderator_account != null) {
        #$news_entry = new NewsEntry();
        #$news_entry->account_id = $moderator_account->id;
        # todo: add more specific type -> I am moderator ...
        #$news_entry->type = NewsEntry::TYPE_DIALOGUE_HAS_STARTED;
        #$news_entry->dialogue_id = $dialogue->id;
        #$news_entry->save($app->get_database());
      }
    }


    return $dialogue;

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
  function: create_dialogue_by_blueprint(...),
  app: App::get(),
);