<?php


namespace cls\controller\request\news;

use App;
use cls\data\attention_profile\NewsEntry;
use cls\RequestError;

class DeleteNewsEntry {

  static function execute(): null|RequestError {

    $news_entry = NewsEntry::get_one(
      App::get_connection(),
      "SELECT * FROM `NewsEntry` WHERE `id` = ?;",
      [$_POST["news_entry_id"]]
    );
    if ($news_entry !== null) {
      $news_entry->delete(App::get_connection());
    }

    return null;
  }

}

