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


# todo: change this fro create message from post
#       to publish draft from membership table ...

function write_message(
  App   $app,
  array $post_data,
): DialogueMessage|RequestError {
  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);

  try {

    if (!$app->somebody_logged_in()) {
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    # todo: check if the sender is allowed to write in this dialogue at this time

    $dialogue_id = $post_data["dialogue_id"] ?? throw new Exception("dialogue_id");
    $content = $post_data["content"] ?? throw new Exception("content");

    // error if content is empty
    if (strlen($content) == 0) {
      return new RequestError(
        dev_message: "Message is empty.",
        code: RequestError::RULE_ERROR,
      );
    }

    $dialogue = Dialogue::get_by_id($app->get_database(), (int)$dialogue_id);
    $all_members = $dialogue->get_memberships($app);

    # todo: check to not insert the same message as before

    // check that same message was not sent before
    $last_message = $dialogue->get_last_message($app);
    if ($last_message && $last_message->content == $content) {
      return new RequestError("Same message was sent before", RequestError::USER_INPUT_ERROR);
    }

    $message = new DialogueMessage();
    $message->account_id = $app->get_currently_logged_in_account()->id;
    $message->dialogue_id = (int)$dialogue_id;
    $message->content = $content;
    $message->create_date = date("Y-m-d H:i:s");

    $message->save($app->get_database());

    foreach ($all_members as $membership) {
      if ($membership->account_id == $app->get_currently_logged_in_account()->id) {
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

  catch (Throwable $e) {
    return new RequestError(
      dev_message: $e->getMessage(),
      code: RequestError::SYSTEM_ERROR,
    );
  }

}

return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: write_message(...),
  app: App::get(),
);
