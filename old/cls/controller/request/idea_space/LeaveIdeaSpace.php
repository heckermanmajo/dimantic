<?php

namespace cls\controller\request\idea_space;

use App;
use cls\data\idea_space\IdeaSpaceMembership;
use cls\RequestError;

class LeaveIdeaSpace {
  static function execute(): IdeaSpaceMembership|RequestError {

    if (!isset($_POST["space_id"])) {
      return new RequestError(
        "Error while applying for space: Space id is missing.",
        RequestError::BAD_REQUEST
      );
    }
    $space_id = $_POST["space_id"];

    $membership = IdeaSpaceMembership::get_one(
      App::get_connection(),
      "SELECT * FROM IdeaSpaceMembership WHERE idea_space_id = :idea_space_id AND account_id = :account_id;",
      [
        "idea_space_id" => $space_id,
        "account_id" => App::get_current_account()->id
      ]
    );

    if ($membership === null) {
      return new RequestError(
        "Error while leaving idea space: You are not a member of this idea space.",
        RequestError::NOT_FOUND
      );
    }

    $membership->left_idea_space = 1;

    $membership->save(App::get_connection());

    return $membership;
  }
}