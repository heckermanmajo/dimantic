<?php


namespace cls\controller\request\post;

use App;
use cls\data\post\Post;
use cls\RequestError;

class CreatePost {
  static function execute(int $idea_space_id): Post|RequestError {

    try {
      $post = new Post();
      if($idea_space_id > 0){
        # todo: check if idea space exists ...
        $post->idea_space_id = $idea_space_id;
      }else{
        return new RequestError(
          "Error while creating post: Idea space id is invalid.",
          RequestError::BAD_REQUEST
        );
      }
      $post->author_id = App::get_current_account()->id;
      $post->content = Post::NEW_POST_TEXT;
      $post->save(App::get_connection());
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

    return $post;
  }
}