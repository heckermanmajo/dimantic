<?php

namespace cls\controller\request\post;

use App;
use cls\data\post\Post;
use cls\RequestError;

class CreateAnswerPost {
  static function execute(): Post|RequestError {

    try {

      $post = Post::get_by_id(App::get_connection(), (int)$_GET["answer_id"]);
      # todo: $_GET["answer_id"] check validity of post ...

      $answer_post = new Post();
      $answer_post->author_id = App::get_current_account()->id;
      $answer_post->parent_post_id = $post->id;
      $post->content = Post::NEW_POST_TEXT;

      \cls\Interpreter::execute_once_commands($answer_post);

      $answer_post->save(App::get_connection());

      # create new entry
      $news_entry = new \cls\data\attention_profile\NewsEntry();
      $news_entry->post_id = $answer_post->id;
      $news_entry->target_member_id = $post->author_id;
      $news_entry->title = App::get_current_account()->name . " has answered to " . $post->get_title();
      $news_entry->answer_post_id = $answer_post->id;
      $news_entry->attention_source = "ownership";
      $news_entry->news_type = "answer_to_my_post";
      $news_entry->save(App::get_connection());

      # todo: send also a news entry to all members of the post
      # todo: also send news to observers of the post

      $observers_of_the_post = \cls\data\account\Account::get_array(
        App::get_connection(),
        "SELECT * FROM `Account` WHERE id IN (
          SELECT owner_member_id FROM AttentionProfile WHERE AttentionProfile.id IN (
            SELECT attention_path_id FROM ObservationEntry WHERE post_id = ?
          )
      );",
        [$post->id]
      );

      foreach ($observers_of_the_post as $observer) {
        $news_entry = new \cls\data\attention_profile\NewsEntry();
        $news_entry->post_id = $answer_post->id;
        $news_entry->target_member_id = $observer->id;
        $news_entry->title = App::get_current_account()->name . " has answered to " . $post->get_title();
        $news_entry->answer_post_id = $answer_post->id;
        $news_entry->attention_source = "observation";
        $news_entry->news_type = "answer_to_observed_post";
        $news_entry->save(App::get_connection());
      }

    }

    catch (\PDOException $t) {
      return new RequestError(
        "Error while logging in: PDOException.",
        RequestError::SYSTEM_ERROR,
        e: $t
      );
    }

    catch (\Throwable $t) {
      return new RequestError(
        "Error while logging in.",
        RequestError::SYSTEM_ERROR,
        e: $t
      );
    }

    return $answer_post;

  }
}
