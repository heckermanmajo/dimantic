<?php
declare(strict_types=1);

use cls\App;
use cls\data\account\NewsEntry;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMessage;
use cls\Protocol;
use cls\RequestError;

if (count(debug_backtrace()) == 0) {
  include $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}

function write_message(
  App   $app,
  array $post_data,
): DialogueMessage|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  # todo: check input and rights ...

  $dialogue_id = $post_data["dialogue_id"] ?? throw new Exception("dialogue_id");
  $content = $post_data["content"] ?? throw new Exception("content");

  $dialogue = Dialogue::get_by_id($app->get_database(), (int)$dialogue_id);
  $all_members = $dialogue->get_memberships($app);

  # todo: check to not insert the same message as before

  // check that same message was not sent before
  $last_message = $dialogue->get_last_message($app);
  if ($last_message->content == $content) {
    return new RequestError("Same message was sent before", RequestError::USER_INPUT_ERROR);
  }

  $message = new DialogueMessage();
  $message->account_id = $app->get_currently_logged_in_account()->id;
  $message->dialogue_id = (int)$dialogue_id;
  $message->content = $content;
  $message->create_date = date("Y-m-d H:i:s");

  $message->save($app->get_database());

  foreach ($all_members as $membership){
    if($membership->account_id == $app->get_currently_logged_in_account()->id){
      continue;
    }
    $news_entry = new NewsEntry();
    $news_entry->account_id = $membership->account_id;
    $news_entry->type = NewsEntry::TYPE_INVITED_TO_NEW_DIALOGUE;
    $news_entry->dialogue_id = $dialogue->id;
    $news_entry->save($app->get_database());
  }

  return $message;

}

return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: write_message(...),
  app: App::get(),
);
