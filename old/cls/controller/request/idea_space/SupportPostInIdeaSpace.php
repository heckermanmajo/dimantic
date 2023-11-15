<?php

namespace cls\controller\request\idea_space;

use App;
use cls\data\post\Post;
use cls\data\post\PostSupportEntry;
use cls\RequestError;

class SupportPostInIdeaSpace {
  static function execute(): RequestError|PostSupportEntry {
    # post_id
    $post_id = $_POST["post_id"];

    $post = Post::get_one(
      App::get_connection(),
      "SELECT * FROM `Post` WHERE `id` = ?;",
      [$post_id]
    );
    # todo: dont create multiple support entries for the same post

    $post_support = new \cls\data\post\PostSupportEntry();
    $post_support->post_id = $post_id;
    $post_support->account_id = App::get_current_account()->id;
    $post_support->season_id = $post->liga_season_id;
    $post_support->create_date = date("Y-m-d H:i:s");

    $post_support->save(App::get_connection());

    return $post_support;

  }
}