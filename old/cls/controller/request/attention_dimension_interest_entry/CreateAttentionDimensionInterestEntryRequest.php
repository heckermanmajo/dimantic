<?php

namespace cls\controller\request\attention_dimension_interest_entry;

use App;
use cls\data\attention_profile\AttentionDimensionInterestEntry;

class CreateAttentionDimensionInterestEntryRequest {
  static function execute(): AttentionDimensionInterestEntry|\cls\RequestError {

    $attention_dimension_id = $_POST["attention_dimension_id"];

    $ad_interest_entry = new AttentionDimensionInterestEntry();

    $ad_interest_entry->attention_profile_id = App::$attention_profile->id;
    $ad_interest_entry->attention_dimension_id = $attention_dimension_id;
    $ad_interest_entry->created_at = date("Y-m-d H:i:s");

    $ad_interest_entry->save(App::get_connection());

    return $ad_interest_entry;
  }
}