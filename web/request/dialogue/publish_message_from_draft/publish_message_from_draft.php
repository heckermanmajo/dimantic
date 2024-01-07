<?php
declare(strict_types=1);

use cls\App;

use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMessage;
use cls\Protocol;
use cls\RequestError;

if (count(debug_backtrace()) == 0) {
  require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}


# todo: change this from create message from post
#       to publish draft from membership table ...

/**
 * @throws Exception
 */
function publish_message_from_draft(
  App   $app,
  array $post_data,
): DialogueMessage|RequestError {

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  # todo: check input and rights ...

  if(!$app->somebody_logged_in()){
    return new RequestError(
      dev_message: "You are not logged in.",
      code: RequestError::RULE_ERROR,
    );
  }

 if (!isset($post_data["dialogue_id"])) {
    return new RequestError(
      dev_message: "\$post_data['dialogue_id'] not set",
      code: RequestError::BAD_REQUEST,
    );
  }

  $dialogue_id = $post_data["dialogue_id"];

  $dialogue = Dialogue::get_by_id($app->get_database(), (int)$dialogue_id);

  $membership = $dialogue->get_membership_of_given_account(
    $app,
    $app->get_currently_logged_in_account()->id,
  );

  $content = $membership->next_message_draft;


  // error if content is empty
  if (strlen($content) == 0) {
    return new RequestError(
      dev_message: "Message is empty.",
      code: RequestError::RULE_ERROR,
    );
  }

  # todo: check that content is not too long or too short

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

  foreach ($all_members as $in_loop_membership){
    if($in_loop_membership->account_id == $app->get_currently_logged_in_account()->id){
      continue;
    }
    # todo: send notification to all members of the dialogue
    #$news_entry = new NewsEntry();
    #$news_entry->account_id = $in_loop_membership->account_id;
    #$news_entry->type = NewsEntry::TYPE_NEW_MESSAGE_IN_DIALOGUE;
    #$news_entry->dialogue_id = $dialogue->id;
    #$news_entry->save($app->get_database());
  }

  // clear the draft message in membership table
  $membership->next_message_draft = "";
  $membership->save($app->get_database());

  return $message;

}

return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: publish_message_from_draft(...),
  app: App::get(),
);
