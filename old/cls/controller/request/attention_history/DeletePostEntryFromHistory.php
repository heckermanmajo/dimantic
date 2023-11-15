<?php


namespace cls\controller\request\attention_history;

use App;
use cls\data\attention_profile\AttentionHistoryEntry;
use cls\RequestError;

class DeletePostEntryFromHistory {
  static function execute(): null|RequestError {
    $post_id = (int)$_POST["post_id"];

    $attention_node = AttentionHistoryEntry::get_one(
      App::get_connection(),
      "SELECT * FROM `AttentionNode` WHERE `attention_path_id` = ? AND `post_id` = ?;",
      [App::$attention_profile->id, $post_id]
    );

    if ($attention_node !== null) {
      $attention_node->delete(App::get_connection());
    }

    return null;

  }
}