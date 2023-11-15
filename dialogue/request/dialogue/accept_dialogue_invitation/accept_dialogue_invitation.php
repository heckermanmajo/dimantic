<?php
declare(strict_types=1);
use cls\App;
use cls\data\account\NewsEntry;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMembership;
use cls\Protocol;
use cls\RequestError;

if (count(debug_backtrace()) == 0) {
  include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}

function accept_dialogue_invitation(
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

  $dialogue = Dialogue::get_by_id(
    $app->get_database(),
    (int) $post_data["dialogue_id"]
  );

  $my_membership = DialogueMembership::get_my_membership_by_dialogue(
    $dialogue->id,
    $app
  );

  $my_membership->state = DialogueMembership::STATE_ACTIVE;
  $my_membership->save($app->get_database());

  $news_entry = new NewsEntry();
  $news_entry->account_id = $dialogue->author_id;
  $news_entry->type = NewsEntry::TYPE_OTHER_PERSON_HAS_ACCEPTED_INVITATION;
  $news_entry->dialogue_id = $dialogue->id;
  $news_entry->save($app->get_database());

  return $my_membership;
}

return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: accept_dialogue_invitation(...),
  app: App::get(),
);