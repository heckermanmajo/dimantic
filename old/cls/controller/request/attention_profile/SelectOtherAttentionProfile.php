<?php


namespace cls\controller\request\attention_profile;

use App;
use cls\data\attention_profile\AttentionProfile;
use cls\RequestError;
use Exception;

class SelectOtherAttentionProfile {
  static function execute(): RequestError|AttentionProfile {

    try {
      $id = $_POST["id"] ?? throw new Exception("No id given.");

      $attention_profile = AttentionProfile::get_one(
        App::get_connection(),
        "SELECT * FROM `AttentionProfile` WHERE `id` = ?;",
        [$id]
      );

      if ($attention_profile == null) {
        throw new Exception("Attention path not found.");
      }

      App::$attention_profile = $attention_profile;
      $_SESSION["selected_attention_profile"] = $attention_profile;
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