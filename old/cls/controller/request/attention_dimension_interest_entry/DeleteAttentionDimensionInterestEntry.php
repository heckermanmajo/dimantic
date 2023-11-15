<?php

namespace cls\controller\request\attention_dimension_interest_entry;

use App;
use cls\data\attention_profile\AttentionDimensionInterestEntry;
use cls\RequestError;

class DeleteAttentionDimensionInterestEntry {
  static function execute(): null|RequestError{
    $attention_dimension_id = $_POST["attention_dimension_id"];

    $attention_dimension_interest_entry = AttentionDimensionInterestEntry::get_one(
      App::get_connection(),
      "SELECT * FROM AttentionDimensionInterestEntry 
         WHERE AttentionDimensionInterestEntry.attention_profile_id = ? 
           AND AttentionDimensionInterestEntry.attention_dimension_id = ?;",
      [
        App::$attention_profile->id,
        $attention_dimension_id
      ]
    );

    $attention_dimension_interest_entry->delete(App::get_connection());

    return null;
  }
}