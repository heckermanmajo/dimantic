<?php

namespace cls\controller\request\idea_space;

use App;
use cls\data\post\Post;
use cls\data\post\PostSupportEntry;

class UnSupportPostIdeaSpace {

  static function execute(){
    # post_id
    $post_id = $_POST["post_id"];

    $post = Post::get_one(
      App::get_connection(),
      "SELECT * FROM `Post` WHERE `id` = ?;",
      [$post_id]
    );
    # todo: dont create multiple support entries for the same post

    $post_support = PostSupportEntry::get_one(
      App::get_connection(),
      "SELECT * FROM `PostSupportEntry` 
            WHERE `post_id` = ? AND `account_id` = ?;",
      [$post_id, App::get_current_account()->id]
    );

    $post_support->delete(App::get_connection());

    return $post_support;
  }

}