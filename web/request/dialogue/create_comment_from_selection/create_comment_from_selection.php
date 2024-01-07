<?php
declare(strict_types=1);
use cls\App;
use cls\data\dialoge\Dialogue;
use cls\data\dialoge\DialogueMembership;
use cls\data\dialoge\DialogueMessage;
use cls\data\dialoge\DialogueMessageComment;
use cls\Protocol;
use cls\RequestError;

if (count(debug_backtrace()) == 0) {
  require $_SERVER["DOCUMENT_ROOT"] . "/cls/App.php";
  App::init_context(basename(__FILE__));
}

function create_comment_from_selection(
  array $post_data,
): DialogueMessageComment|RequestError {

  [$log, $warn, $err, $todo] = App::get_logging_functions(__CLASS__, __FUNCTION__, __FILE__, __LINE__);
  try {
    
    $app = App::get();

    if(!$app->somebody_logged_in()){
      return new RequestError(
        dev_message: "You are not logged in.",
        code: RequestError::RULE_ERROR,
      );
    }

    if (!isset($post_data["dialogue_message_id"])) {
      return new RequestError(
        dev_message: "\$post_data[\"dialogue_message_id\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }
    $dialogue_message_id = (int)$post_data["dialogue_message_id"];

    if(!isset($post_data["selection"])){
      return new RequestError(
        dev_message: "\$post_data[\"selection\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }
    $selection = $post_data["selection"];

    if(!isset($post_data["comment_text"])){
      return new RequestError(
        dev_message: "\$post_data[\"comment_text\"] not set",
        code: RequestError::BAD_REQUEST,
      );
    }
    $comment_text = (string)$post_data["comment_text"];

    $message = DialogueMessage::get_by_id(
      $app->get_database(),
      $dialogue_message_id
    );

    if($message == null){
      return new RequestError(
        dev_message: "Message with id $dialogue_message_id not found",
        code: RequestError::BAD_REQUEST,
      );
    }

    $dialogue = Dialogue::get_by_id(
      $app->get_database(),
      $message->dialogue_id
    );

    if(!$dialogue->current_user_is_member($app)){
      $membership = DialogueMembership::get_my_membership_by_dialogue(
        $dialogue->id,
        $app
      );
      if ($membership == null) {
        return new RequestError(
          dev_message: "You are not a member of this dialogue; is the current user is member check broken?",
          code: RequestError::SYSTEM_ERROR,
          user_message: "You are not a member of this dialogue; This seems to be a bug",
        );
      }
      if ($membership->state != DialogueMembership::STATE_ACTIVE) {
        return new RequestError(
          dev_message: "Your membership is not accepted",
          code: RequestError::SYSTEM_ERROR,
        );
      }
    }

    // dont insert the same comment twice
    $comments = $message->get_message_comments($app);
    $last_comment = end($comments);
    if($last_comment != null && $last_comment->selection == $selection && $last_comment->comment_text == $comment_text){
      return new RequestError(
        dev_message: "This comment is already inserted",
        code: RequestError::BAD_REQUEST,
      );
    }

    $comment = new DialogueMessageComment();
    $comment->dialogue_message_id = $message->id;
    $comment->account_id = $app->get_currently_logged_in_account()->id;
    $comment->selection = $selection;
    $comment->comment_text = $comment_text;
    $comment->create_date = date("Y-m-d H:i:s");
    $comment->save($app->get_database());

    # todo: do we want news entries for comments on messages?

    return $comment;

  } catch (Exception $e) {
    return new RequestError(
      dev_message: $e->getMessage(),
      code: RequestError::SYSTEM_ERROR,
    );
  }

}

return Protocol::request(
  is_called_directly: count(debug_backtrace()) == 0,
  function: create_comment_from_selection(...),
);