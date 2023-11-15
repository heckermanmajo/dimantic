<?php

namespace cls\data\league;

use App;
use cls\RequestError;

trait CreateAttentionDimensionRequest {
  static function create_attention_dimension_request(): AttentionDimension|RequestError {

    try {
      $attention_dimension = new AttentionDimension();
      $attention_dimension->title = $_POST["title"];
      $attention_dimension->description = $_POST["description"];
      $attention_dimension->author_member_id = App::get_current_account()->id;
      $attention_dimension->save(App::get_connection());
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

    return $attention_dimension;
  }
}