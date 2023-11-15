<?php

namespace cls\controller\request\post;

use App;
use cls\data\post\Post;
use cls\RequestError;

class PublishPost {
  static function execute(): RequestError|Post {
    $post_id = $_POST["post_id"];

    $post = \cls\data\post\Post::get_one(
      App::get_connection(),
      "SELECT * FROM Post WHERE Post.id = ?;",
      [$post_id]
    );

    $post->published = 1;

    $post->save(App::get_connection());

    return $post;
  }
}