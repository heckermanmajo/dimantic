<?php
declare(strict_types=1);

use cls\App;
use cls\data\account\Account;
use cls\data\account\NewsEntry;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMembership;
use cls\Protocol;
use cls\RequestError;


if (count(debug_backtrace()) == 0) {
  include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


function invite_into_dialoge(
  App   $app,
  array $post_data,
): DialogueMembership|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__);

  if (!isset($post_data["dialogue_id"])) {
    return new RequestError(
      dev_message: "\$post_data[\"dialogue_id\"] not set",
      code: RequestError::BAD_REQUEST,
    );
  }
  if (!isset($post_data["account_id"])) {
    return new RequestError(
      dev_message: "\$post_data[\"account_id\"] not set",
      code: RequestError::BAD_REQUEST,
    );
  }

  $dialogue_id = (int)$post_data["dialogue_id"];
  $account_id = (int)$post_data["account_id"];

  $dialogue = Dialogue::get_by_id($app->get_database(), $dialogue_id);

  if ($dialogue->get_number_of_memberships($app) >= $dialogue->number_of_needed_members) {
    return new RequestError(
      dev_message: "Dialogue is full.",
      code: RequestError::RULE_ERROR,
    );
  }

  # todo: check that account is not author, not already member, etc.
  $account = Account::get_by_id($app->get_database(), $account_id);
  $todo("we dont check that account is not author");

  $membership = new DialogueMembership();
  $membership->account_id = $account->id;
  $membership->dialogue_id = $dialogue->id;
  $membership->type = DialogueMembership::TYPE_INVITATION;
  $membership->state = DialogueMembership::STATE_PENDING;
  $membership->save($app->get_database());

  $news_entry = new NewsEntry();
  $news_entry->account_id = $account->id;
  $news_entry->type = NewsEntry::TYPE_INVITED_TO_NEW_DIALOGUE;
  $news_entry->dialogue_id = $dialogue->id;
  $news_entry->save($app->get_database());


  return $membership;

}

return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: invite_into_dialoge(...),
  app: App::get(),
);