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

function create_dialogue(
  App   $app,
  array $post_data,
): Dialogue|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__);
  #if (!isset($post_data["username_or_email"])) {
  #  return new RequestError(
  #    dev_message: "\$post_data[\"username_or_email\"] not set",
  #    code: RequestError::BAD_REQUEST,
  #  );
  #}

  // create new dialogue
  $dialogue = new Dialogue();
  $dialogue->content = "";
  $dialogue->state = Dialogue::STATE_NOT_YET_STARTED;
  $dialogue->author_id = $app->get_currently_logged_in_account()->id;
  $dialogue->number_of_needed_members = 1; # todo: change later ...
  $dialogue->create_date = date("Y-m-d H:i:s");
  $dialogue->save($app->get_database());

  $membership = new DialogueMembership();
  $membership->account_id = $app->get_currently_logged_in_account()->id;
  $membership->dialogue_id = $dialogue->id;
  $membership->type = DialogueMembership::TYPE_CREATOR;
  $membership->state = DialogueMembership::STATE_ACTIVE;
  $membership->create_date = date("Y-m-d H:i:s");
  $membership->save($app->get_database());

  if(isset($post_data["partner_id"])){
    $log("partner_id is set, create invitation for partner with id {$post_data['partner_id']}");
    $partner_id = (int)$post_data["partner_id"];
    $partner = Account::get_by_id($app->get_database(), $partner_id);

    $membership = new DialogueMembership();
    $membership->account_id = $partner->id;
    $membership->dialogue_id = $dialogue->id;
    $membership->type = DialogueMembership::TYPE_INVITATION;
    $membership->state = DialogueMembership::STATE_PENDING;
    $membership->save($app->get_database());

    $news_entry = new NewsEntry();
    $news_entry->account_id = $partner->id;
    $news_entry->type = NewsEntry::TYPE_INVITED_TO_NEW_DIALOGUE;
    $news_entry->dialogue_id = $dialogue->id;
    $news_entry->save($app->get_database());

  }

  return $dialogue;

}


return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: create_dialogue(...),
  app: App::get(),
);