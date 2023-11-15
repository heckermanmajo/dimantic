<?php


namespace cls\controller\request\attention_profile;

use App;
use cls\data\attention_profile\AttentionProfile;
use cls\RequestError;

class CreateAttentionProfile {
  static function execute(): RequestError|AttentionProfile {

    try {
      $attention_profile = new AttentionProfile();
      $attention_profile->owner_member_id = App::get_current_account()->id;
      $attention_profile->title = $_POST["title"];
      $attention_profile->description = $_POST["description"];
      $attention_profile->created_at = date("Y-m-d H:i:s");
      $attention_profile->save(App::get_connection());
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

    return $attention_profile;
  }
}