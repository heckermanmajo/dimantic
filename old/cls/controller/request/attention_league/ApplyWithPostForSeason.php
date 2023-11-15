<?php

namespace cls\controller\request\attention_league;

use cls\data\post\Post;
use cls\RequestError;

class ApplyWithPostForSeason {
  static function execute(): RequestError|Post{
    $season_id = $_POST["season_id"];
    $post_id = $_POST["post_id"];
    $post = Post::get_by_id(
      \App::get_connection(),
      $post_id
    );

    assert($post->liga_season_id == 0);
    $post->liga_season_id = $season_id;

    $post->save(\App::get_connection());

    return $post;
  }
}